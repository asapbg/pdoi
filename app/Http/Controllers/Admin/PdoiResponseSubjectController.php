<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PdoiResponseSubjectStoreRequest;
use App\Imports\PdoiSubjectImport;
use App\Mail\AlertForSubjectChanges;
use App\Models\EkatteArea;
use App\Models\EkatteMunicipality;
use App\Models\EkatteSettlement;
use App\Models\File;
use App\Models\PdoiResponseSubject;
use App\Models\RzsSection;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class PdoiResponseSubjectController extends AdminController
{
    const LIST_ROUTE = 'admin.rzs';
    const EDIT_ROUTE = 'admin.rzs.edit';
    const DELETE_ROUTE = 'admin.rzs.delete';
    const STORE_ROUTE = 'admin.rzs.store';
    const LIST_VIEW = 'admin.rzs.index';
    const EDIT_VIEW = 'admin.rzs.edit';
    const EXAMPLE_IMPORT_FILE = 'import_rzs_template_file.xlsx';

    public function index(Request $request)
    {
        $requestFilter = $request->all();
        $filter = $this->filters($request);
        $paginate = $filter['paginate'] ?? PdoiResponseSubject::PAGINATE;

        if( !isset($requestFilter['active']) ) {
            $requestFilter['active'] = 1;
        }
        $items = PdoiResponseSubject::with(['translation', 'section.translation'])
            ->FilterBy($requestFilter)
            ->paginate($paginate);
        $toggleBooleanModel = 'PdoiResponseSubject';
        $deleteRouteName = self::DELETE_ROUTE;
        $editRouteName = self::EDIT_ROUTE;
        $listRouteName = self::LIST_ROUTE;

        return $this->view(self::LIST_VIEW, compact('filter', 'items', 'toggleBooleanModel', 'deleteRouteName', 'editRouteName', 'listRouteName'));
    }

    public function import(Request $request)
    {
        $myErrors = [];
        if( $request->isMethod('post') ) {
            //validate file
            $validator = Validator::make($request->all(), [
                'file' => ['required', 'file', 'max:'.config('filesystems.max_upload_file_size'), 'mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
            ]);
            if( $validator->fails() ){
                return back()->withErrors($validator->errors());
            }
            DB::beginTransaction();
            try {
                $validated = $validator->validated();
                Excel::import(new PdoiSubjectImport(), $validated['file']->store('temp'));
                DB::commit();
                return redirect()->route('admin.rzs.import');
            } catch (\Exception $e) {
                DB::rollBack();
                logError('Import RZS', $e->getMessage());
                return redirect()->route('admin.rzs.import')->with('danger', 'Възникна грешка при импортирането, моля опитайте отново');
            }

        }
        return $this->view('admin.rzs.import', compact('myErrors'));
    }

    /**
     * @param Request $request
     * @param PdoiResponseSubject $item
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, PdoiResponseSubject $item)
    {
        if( ($item->id && ($request->user()->cannot('update', $item) && $request->user()->cannot('updateSettings', $item)) )
            || $request->user()->cannot('create', PdoiResponseSubject::class) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }
        $subjects = PdoiResponseSubject::optionsList($item->id ?? 0);
        $rzsSections = RzsSection::optionsList();
        $areas = EkatteArea::optionsList();
        $municipalities = EkatteMunicipality::optionsList();
        $settlement = EkatteSettlement::optionsList();
        $courtSubjects = PdoiResponseSubject::optionsList();

        $storeRouteName = self::STORE_ROUTE;
        $listRouteName = self::LIST_ROUTE;
        $translatableFields = PdoiResponseSubject::translationFieldsProperties();
        $editOptions = [
            'full' => $request->user()->can('update', $item),
            'settings' => $request->user()->can('updateSettings', $item),
        ];

        $this->setTitlePlural($item->id > 0 ? 'Редакция на ЗС ('.$item->subject_name.')' : 'Създаване на ЗС');

        return $this->view(self::EDIT_VIEW, compact('item', 'storeRouteName',
            'listRouteName', 'translatableFields', 'areas', 'municipalities', 'settlement', 'rzsSections',
            'courtSubjects', 'subjects', 'editOptions'));
    }

    public function store(PdoiResponseSubjectStoreRequest $request, PdoiResponseSubject $item)
    {
//        $r = new PdoiResponseSubjectStoreRequest();
//        $validator = Validator::make($request->all(), $r->rules());
//        dd($request->all(),$validator->errors());
        $id = $item->id;
        $validated = $request->validated();
        if( ($item->id && ($request->user()->cannot('update', $item) && $request->user()->cannot('updateSettings', $item)) )
            || $request->user()->cannot('create', PdoiResponseSubject::class) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }
        try {
            $validated['date_from'] = Carbon::now();//for now
            $validated['delivery_method'] = $validated['rzs_delivery_method'];
            $validated['court_id'] = $validated['court'];
            unset($validated['rzs_delivery_method'], $validated['court']);

            $fillable = $this->getFillableValidated($validated, $item);
            if( !$id ) {
                $fillable['adm_register'] = 0;
            }
            $fillable['redirect_only'] = $fillable['redirect_only'] ?? 0;

            //Check if need to alert admins and moderators about subject changes
            if( $id ){
                if( ($fillable['adm_level'] != $item->adm_level) || ($fillable['parent_id'] != $item->parent_id) ) {
                    if( env('APP_ENV') != 'production' ) {
                        $emailList =[env('LOCAL_TO_MAIL')];
                    } else {
                        $emailList = $item->getAlertUsersEmail();
                    }
                    if( sizeof($emailList) ) {
                        $mailData = array(
                            'subject' => $item
                        );
                        if( $fillable['adm_level'] != $item->adm_level ) {
                            $mailData['new_level'] = RzsSection::find((int)$fillable['adm_level']);
                        }
                        if( $fillable['parent_id'] != $item->parent_id ) {
                            $mailData['new_parent'] = PdoiResponseSubject::find((int)$fillable['parent_id']);
                        }
                        Mail::to($emailList)->send(new AlertForSubjectChanges($mailData));
                    }
                }
            }


            $item->fill($fillable);
            $item->save();
            $this->storeTranslateOrNew(PdoiResponseSubject::TRANSLATABLE_FIELDS, $item, $validated);

            if( $id ) {
                return redirect(route(self::EDIT_ROUTE, $item) )
                    ->with('success', trans_choice('custom.rzs_items', 1)." ".__('messages.updated_successfully_m'));
            }

            return to_route(self::LIST_ROUTE)
                ->with('success', trans_choice('custom.rzs_items', 1)." ".__('messages.created_successfully_m'));
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->withInput(request()->all())->with('danger', __('messages.system_error'));
        }

    }

    public function delete(Request $request, PdoiResponseSubject $item)
    {
        if( $request->user()->cannot('delete', $item) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }
        try {
            $item->active = 0;
            $item->save();
            $item->delete();

            return to_route(self::LIST_ROUTE)
                ->with('success', trans_choice('custom.rzs_items', 1)." ".__('messages.deleted_successfully_m'));
        }
        catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->withInput(request()->all())->with('danger', __('messages.system_error'));
        }
    }

    public function downloadImportTemplateFile(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\RedirectResponse
    {
        $file = File::PUBLIC_UPLOAD_EXAMPLES_DIR.self::EXAMPLE_IMPORT_FILE;
        if (Storage::disk('public_uploads')->has($file)) {
            return Storage::disk('public_uploads')->download($file);
        } else {
            return back()->with('warning', __('custom.record_not_found'));
        }

    }

    private function filters($request)
    {
        return array(
            'name' => array(
                'type' => 'text',
                'placeholder' => __('validation.attributes.name'),
                'value' => $request->input('name'),
                'col' => 'col-md-4'
            ),
            'eik' => array(
                'type' => 'text',
                'placeholder' => __('validation.attributes.eik'),
                'value' => $request->input('eik'),
                'col' => 'col-md-3'
            ),
            'manual' => array(
                'type' => 'checkbox',
                'label' => __('validation.attributes.manual_rzs'),
                'value' => 1,
                'checked' => (int)$request->input('manual'),
                'col' => 'col-md-3'
            )
        );
    }

    /**
     * @param $id
     * @param array $with
     */
    private function getRecord($id, array $with = []): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Builder|array|null
    {
        $qItem = PdoiResponseSubject::query();
        if( sizeof($with) ) {
            $qItem->with($with);
        }
        $item = $qItem->find((int)$id);
        if( !$item ) {
            abort(Response::HTTP_NOT_FOUND);
        }
        return $item;
    }

}
