<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PdoiResponseSubjectStoreRequest;
use App\Models\PdoiResponseSubject;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class PdoiResponseSubjectController extends AdminController
{
    public function index(Request $request)
    {
        $filter = $this->filters($request);
        $items = PdoiResponseSubject::with(['translation', 'parent'])
            ->IsActive()
            ->FilterBy($request->all())
            ->get();
        $toggleBooleanModel = 'EgovOrganisation';
        $deleteRouteName = 'admin.pdo_subjects.delete';
        $editRouteName = 'admin.pdo_subjects.edit';
        return $this->view('admin.pdoi_subjects.index', compact('filter', 'items', 'toggleBooleanModel', 'deleteRouteName', 'editRouteName'));
    }

    /**
     * @param Request $request
     * @param PdoiResponseSubject $item
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, PdoiResponseSubject $item)
    {
        if( ($item && $request->user()->cannot('update', $item)) || $request->user()->cannot('create', PdoiResponseSubject::class) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }
        $subjects = PdoiResponseSubject::optionsList();
        $storeRouteName = 'admin.pdo_subjects.store';
        $listRouteName = 'admin.pdo_subjects';
        return $this->view('admin.pdoi_subjects.edit', compact('item', 'storeRouteName', 'listRouteName', 'subjects'));
    }

    public function store(PdoiResponseSubjectStoreRequest $request)
    {
        $validated = $request->validated();
        if( $request->isMethod('put') ) {
            $item = $this->getRecord($validated['id'], ['translation']);
        } else {
            $item = new PdoiResponseSubject();
        }

        if( ($request->isMethod('put') && $request->user()->cannot('update', $item)) || $request->user()->cannot('create', PdoiResponseSubject::class) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }

        try {
            $fillable = $this->getFillableValidated($validated, $item);
            if( $request->isMethod('post') ) {
                $fillable['adm_register'] = 1;
            }
            $fillable['redirect_only'] = $fillable['redirect_only'] ?? 0;

            $item->fill($fillable);
            $item->save();
            $this->storeTranslateOrNew($item->getFillable(), $item, $validated);

            if( $request->isMethod('put') ) {
                return redirect(route('admin.pdo_subjects.edit', $item) )
                    ->with('success', trans_choice('custom.pdo_subjects', 1)." ".__('messages.updated_successfully_m'));
            }

            return to_route('admin.pdo_subjects')
                ->with('success', trans_choice('custom.pdo_subjects', 1)." ".__('messages.created_successfully_m'));
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

            return to_route('admin.pdo_subjects')
                ->with('success', trans_choice('custom.pdo_subjects', 1)." ".__('messages.deleted_successfully_m'));
        }
        catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->withInput(request()->all())->with('danger', __('messages.system_error'));
        }
    }

    private function filters($request)
    {
        return array(
            'name' => array(
                'type' => 'text',
                'placeholder' => __('validation.attributes.name'),
                'value' => $request->input('name')
            ),
            'eik' => array(
                'type' => 'text',
                'placeholder' => __('validation.attributes.eik'),
                'value' => $request->input('eik')
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
