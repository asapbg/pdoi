<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CustomStatisticTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomStatisticStoreRequest;
use App\Models\CustomStatistic;
use App\Models\File;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CustomStatisticController extends AdminController
{
    const LIST_ROUTE = 'admin.custom_statistic';
    const EDIT_ROUTE = 'admin.custom_statistic.edit';
    const STORE_ROUTE = 'admin.custom_statistic.store';
    const DELETE_ROUTE = 'admin.custom_statistic.delete';
    const LIST_VIEW = 'admin.custom_statistic.index';
    const EDIT_VIEW = 'admin.custom_statistic.edit';

    public function index(Request $request)
    {
        $requestFilter = $request->all();
        $filter = $this->filters($request);
        $paginate = $filter['paginate'] ?? CustomStatistic::PAGINATE;

        if( !isset($requestFilter['active']) ) {
            $requestFilter['active'] = 1;
        }
        $items = CustomStatistic::with(['translation'])
            ->FilterBy($requestFilter)
            ->orderByTranslation('name')
            ->paginate($paginate);
        $editRouteName = self::EDIT_ROUTE;
        $listRouteName = self::LIST_ROUTE;
        $deleteRouteName = self::DELETE_ROUTE;

        return $this->view(self::LIST_VIEW, compact('filter', 'items', 'editRouteName', 'listRouteName', 'deleteRouteName'));
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, $id = 0)
    {
        $item = $id ? CustomStatistic::find($id) : new CustomStatistic();

        if($id && !$item){
            abort(Response::HTTP_NOT_FOUND);
        }

        if( ($item->id && $request->user()->cannot('update', $item)) || (!$item->id && $request->user()->cannot('create', CustomStatistic::class)) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }
        $storeRouteName = self::STORE_ROUTE;
        $listRouteName = self::LIST_ROUTE;
        $translatableFields = CustomStatistic::translationFieldsProperties();
        return $this->view(self::EDIT_VIEW, compact('item', 'storeRouteName', 'listRouteName', 'translatableFields'));
    }

    /**
     * @param CustomStatisticStoreRequest $request
     * @param int $id
     * @return Application|RedirectResponse|Redirector
     */
    public function store(Request $request, $id = 0)
    {
        $vr = new CustomStatisticStoreRequest();
        $validator = Validator::make($request->all(), $vr->rules());
        if($validator->fails()){
            return back()->withInput()->withErrors($validator->errors())->with('danger', __('custom.check_for_errors'));
        }

        $item = $id ? CustomStatistic::find($id) : new CustomStatistic();

        if($id && !$item){
            abort(Response::HTTP_NOT_FOUND);
        }

        $validated = $validator->validated();

        if( ($id && $request->user()->cannot('update', $item))
            || (!$id && $request->user()->cannot('create', CustomStatistic::class)) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }

        try {
            if(isset($validated['file'])){
                //TODO STATISTIC get data from file
                $fData = csvToArray($validated['file']->getPathName());
                if(!sizeof($fData)){
                    return back()->withInput()->with('danger', 'Данните от посочения файл не отговарят на тип справка');
                }

                //data without header
                $fDataNoHeader = $fData;
                array_shift($fDataNoHeader);
                //heading row containing secondary labels
                $datasetsLabels = $fData[0];

                //Main labels
                $labels = array_map(function($csvRow){
                    return $csvRow[0];
                }, $fDataNoHeader);

                $datasets = [];
                foreach ($datasetsLabels as $hKey => $hCell){
                    if($hKey){
                        $datasets[] = array(
                            "label" => $hCell,
                            "data" => array_map(function($csvRow) use($hKey) {
                                return $csvRow[$hKey];
                            }, $fDataNoHeader)
                        );
                    }
                }

                $sData = array(
                    "labels" => $labels,
                    "datasets" => $datasets
                );
            }

            if(!$id){
                $validated['user_id'] = auth()->user()->id;
            }

            $fillable = $this->getFillableValidated($validated, $item);
            $item->fill($fillable);
            if(isset($sData)){
                $item->data = json_encode($sData, JSON_UNESCAPED_UNICODE);
            }
            $item->save();
            $this->storeTranslateOrNew(CustomStatistic::TRANSLATABLE_FIELDS, $item, $validated);

            return redirect(route(self::EDIT_ROUTE, $item) )
                ->with('success', trans_choice('custom.pages', 1)." ".($id ? __('messages.updated_successfully_m') : __('messages.created_successfully_m')));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withInput(request()->all())->with('danger', __('messages.system_error'));
        }

    }

    public function delete(CustomStatistic $item)
    {
        try {
            $item->delete();

            return to_route('admin.custom_statistic')
                ->with('success', trans_choice('custom.custom_statistics', 1)." ".__('messages.deleted_successfully_m'));
        }
        catch (\Exception $e) {

            Log::error($e);
            return to_route('admin.users')->with('danger', __('messages.system_error'));

        }
    }

    public function downloadExampleStatisticFile(Request $request, $type)
    {
        $file = File::PUBLIC_UPLOAD_EXAMPLES_DIR.CustomStatisticTypeEnum::fileExamplesByValue($type);
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
            )
        );
    }

}
