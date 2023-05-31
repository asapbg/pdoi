<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EgovOrganisationStoreRequest;
use App\Models\EgovOrganisation;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EgovOrganisationController extends Controller
{
    public function index(Request $request)
    {
        $filter = $this->filters($request);
        $items = EgovOrganisation::IsActive()->FilterBy($request->all())->get();
        $toggleBooleanModel = 'EgovOrganisation';
        $deleteRouteName = 'admin.subjects.delete';
        $editRouteName = 'admin.subjects.edit';
        return $this->view('admin.egov_oragnizations.index', compact('filter', 'items', 'toggleBooleanModel', 'deleteRouteName', 'editRouteName'));
    }

    /**
     * @param EgovOrganisationStoreRequest $request
     * @param EgovOrganisation $item
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(EgovOrganisationStoreRequest $request, EgovOrganisation $item)
    {
        if( ($item && $request->user()->cannot('update', $item)) || $request->user()->cannot('create', EgovOrganisation::class) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }
        return $this->view('admin.egov_oragnizations.edit', compact('item'));
    }

    public function store(EgovOrganisationStoreRequest $request)
    {
        $validated = $request->validated();
        if( $request->isMethod('put') ) {
            $item = $this->getRecord();
        } else {
            $item = new EgovOrganisation();
        }

        if( ($request->isMethod('put') && $request->user()->cannot('update', $item)) || $request->user()->cannot('create', EgovOrganisation::class) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }

        unset($validated['id']);
        $item->fill($validated);
        $item->save();
    }

    public function delete(Request $request, EgovOrganisation $item)
    {
        if( $request->user()->cannot('delete', $item) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }
        try {
            $item->active = 0;
            $item->save();
            $item->delete();

            return to_route('admin.subjects')
                ->with('success', trans_choice('custom.subjects', 1)." ".__('messages.deleted_successfully_m'));
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
        $qItem = EgovOrganisation::query();
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
