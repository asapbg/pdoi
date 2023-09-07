<?php

namespace App\Http\Controllers\Admin\Nomenclature;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Controller;
use App\Http\Requests\CountryStoreRequest;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CountryController extends AdminController
{
    const LIST_ROUTE = 'admin.nomenclature.ekatte.country';
    const EDIT_ROUTE = 'admin.nomenclature.ekatte.country.edit';
    const STORE_ROUTE = 'admin.nomenclature.ekatte.country.store';
    const LIST_VIEW = 'admin.nomenclatures.ekatte.country.index';
    const EDIT_VIEW = 'admin.nomenclatures.ekatte.country.edit';
    const EXPORT_TYPE = 'country';

    public function index(Request $request)
    {
        $export = $request->filled('export');
        $requestFilter = $request->all();
        $filter = $this->filters($request);
        $paginate = $filter['paginate'] ?? Country::PAGINATE;

        if( !isset($requestFilter['active']) ) {
            $requestFilter['active'] = 1;
        }
        $q = Country::with(['translation'])
            ->FilterBy($requestFilter)
            ->orderByTranslation('name');

        if( $export ) {
            return $this->getData($q, self::EXPORT_TYPE);
        } else {
            $items = $q->paginate($paginate);
        }

        $toggleBooleanModel = 'Country';
        $editRouteName = self::EDIT_ROUTE;
        $listRouteName = self::LIST_ROUTE;

        return $this->view(self::LIST_VIEW, compact('filter', 'items', 'toggleBooleanModel', 'editRouteName', 'listRouteName'));
    }

    /**
     * @param Request $request
     * @param Country $item
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, Country $item)
    {
        if( ($item && $request->user()->cannot('update', $item)) || $request->user()->cannot('create', Country::class) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }
        $storeRouteName = self::STORE_ROUTE;
        $listRouteName = self::LIST_ROUTE;
        $translatableFields = Country::translationFieldsProperties();

        $title = $item->id > 0 ? __('custom.edit_object', ['object' => trans_choice('custom.countries', 1), 'object_name' => $item->name]) : __('custom.create_object', ['object' => trans_choice('custom.countries', 1)]);
        $this->setTitlePlural($title);
        $this->setBreadcrumbsTitle($title);
        return $this->view(self::EDIT_VIEW, compact('item', 'storeRouteName', 'listRouteName', 'translatableFields'));
    }

    public function store(CountryStoreRequest $request, Country $item)
    {
        $id = $item->id;
        $validated = $request->validated();
        if( ($id && $request->user()->cannot('update', $item))
            || $request->user()->cannot('create', Country::class) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }

        try {
            $fillable = $this->getFillableValidated($validated, $item);
            $item->fill($fillable);
            $item->save();
            $this->storeTranslateOrNew(Country::TRANSLATABLE_FIELDS, $item, $validated);

            if( $id ) {
                return redirect(route(self::EDIT_ROUTE, $item) )
                    ->with('success', trans_choice('custom.nomenclature.country', 1)." ".__('messages.updated_successfully_m'));
            }

            return to_route(self::LIST_ROUTE)
                ->with('success', trans_choice('custom.nomenclature.country', 1)." ".__('messages.created_successfully_m'));
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
                'value' => $request->input('name'),
                'col' => 'col-md-4'
            )
        );
    }

    /**
     * @param $id
     * @param array $with
     */
    private function getRecord($id, array $with = []): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Builder|array|null
    {
        $qItem = Country::query();
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
