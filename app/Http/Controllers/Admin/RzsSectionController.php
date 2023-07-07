<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RzsSectionStoreRequest;
use App\Models\RzsSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RzsSectionController extends AdminController
{
    const LIST_ROUTE = 'admin.rzs.sections';
    const EDIT_ROUTE = 'admin.rzs.sections.edit';
    const STORE_ROUTE = 'admin.rzs.sections.store';
    const LIST_VIEW = 'admin.rzs_section.index';
    const EDIT_VIEW = 'admin.rzs_section.edit';

    public function index(Request $request)
    {
        $requestFilter = $request->all();
        $filter = $this->filters($request);
        $paginate = $filter['paginate'] ?? RzsSection::PAGINATE;

        if( !isset($requestFilter['active']) ) {
            $requestFilter['active'] = 1;
        }
        $items = RzsSection::with(['subjects', 'translation'])
            ->FilterBy($requestFilter)
            ->paginate($paginate);
        $toggleBooleanModel = 'RzsSection';
        $editRouteName = self::EDIT_ROUTE;
        $listRouteName = self::LIST_ROUTE;

        return $this->view(self::LIST_VIEW, compact('filter', 'items', 'toggleBooleanModel', 'editRouteName', 'listRouteName'));
    }

    /**
     * @param Request $request
     * @param RzsSection $item
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, RzsSection $item)
    {
        if( ($item && $request->user()->cannot('update', $item)) || $request->user()->cannot('create', RzsSection::class) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }

        $storeRouteName = self::STORE_ROUTE;
        $listRouteName = self::LIST_ROUTE;
        $translatableFields = RzsSection::translationFieldsProperties();
        $rzsSections = RzsSection::optionsList();
        return $this->view(self::EDIT_VIEW, compact('item', 'storeRouteName',
            'listRouteName', 'translatableFields', 'rzsSections'));
    }

    public function store(RzsSectionStoreRequest $request, RzsSection $item)
    {
        $id = $item->id;
        $validated = $request->validated();
        if( ($id && $request->user()->cannot('update', $item)) || $request->user()->cannot('create', RzsSection::class) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }

        try {
            $fillable = $this->getFillableValidated($validated, $item);
            if( !$id ) {
                $fillable['system_name'] = 'custom';
            }
            $item->fill($fillable);
            $item->save();

            if( !$id ) {
                $item->manual = 1;
                $item->adm_level = $item->id;
                $item->save();
            }

            $this->storeTranslateOrNew(RzsSection::TRANSLATABLE_FIELDS, $item, $validated);

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

    private function filters($request)
    {
        return array(
            'name' => array(
                'type' => 'text',
                'placeholder' => __('validation.attributes.name'),
                'value' => $request->input('name')
            ),
        );
    }

    /**
     * @param $id
     * @param array $with
     */
    private function getRecord($id, array $with = []): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Builder|array|null
    {
        $qItem = RzsSection::query();
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
