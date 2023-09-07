<?php

namespace App\Http\Controllers\Admin\Nomenclature;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Requests\EkatteMunicipalityStoreRequest;
use App\Models\EkatteMunicipality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EkatteMunicipalityController extends AdminController
{
    const LIST_ROUTE = 'admin.nomenclature.ekatte.municipality';
    const EDIT_ROUTE = 'admin.nomenclature.ekatte.municipality.edit';
    const STORE_ROUTE = 'admin.nomenclature.ekatte.municipality.store';
    const LIST_VIEW = 'admin.nomenclatures.ekatte.municipality.index';
    const EDIT_VIEW = 'admin.nomenclatures.ekatte.municipality.edit';
    const EXPORT_TYPE = 'municipality';

    public function index(Request $request)
    {
        $export = $request->filled('export');
        $requestFilter = $request->all();
        $filter = $this->filters($request);
        $paginate = $filter['paginate'] ?? EkatteMunicipality::PAGINATE;

        if( !isset($requestFilter['active']) ) {
            $requestFilter['active'] = 1;
        }
        $q = EkatteMunicipality::with(['translation'])
            ->FilterBy($requestFilter)
            ->orderByTranslation('ime');

        if( $export ) {
            return $this->getData($q, self::EXPORT_TYPE);
        } else {
            $items = $q->paginate($paginate);
        }

        $toggleBooleanModel = 'EkatteMunicipality';
        $editRouteName = self::EDIT_ROUTE;
        $listRouteName = self::LIST_ROUTE;

        return $this->view(self::LIST_VIEW, compact('filter', 'items', 'toggleBooleanModel', 'editRouteName', 'listRouteName'));
    }

    /**
     * @param Request $request
     * @param EkatteMunicipality $item
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, EkatteMunicipality $item)
    {
        if( ($item && $request->user()->cannot('update', $item)) || $request->user()->cannot('create', EkatteMunicipality::class) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }
        $storeRouteName = self::STORE_ROUTE;
        $listRouteName = self::LIST_ROUTE;
        $translatableFields = EkatteMunicipality::translationFieldsProperties();

        $title = $item->id > 0 ? __('custom.edit_object', ['object' => trans_choice('custom.municipality', 1), 'object_name' => $item->ime]) : __('custom.create_object', ['object' => trans_choice('custom.municipality', 1)]);
        $this->setTitlePlural($title);
        $this->setBreadcrumbsTitle($title);

        return $this->view(self::EDIT_VIEW, compact('item', 'storeRouteName', 'listRouteName', 'translatableFields'));
    }

    public function store(EkatteMunicipalityStoreRequest $request,EkatteMunicipality $item)
    {
        $id = $item->id;
        $validated = $request->validated();

        if( ($id && $request->user()->cannot('update', $item))
            || $request->user()->cannot('create', EkatteMunicipality::class) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }

        try {
            $fillable = $this->getFillableValidated($validated, $item);

            if( !$id ) {
                $fillable['valid'] = 'Y';
            }
            $item->fill($fillable);
            $item->save();
            $this->storeTranslateOrNew(EkatteMunicipality::TRANSLATABLE_FIELDS, $item, $validated);

            if( $id ) {
                return redirect(route(self::EDIT_ROUTE, $item) )
                    ->with('success', trans_choice('custom.nomenclature.municipalities', 1)." ".__('messages.updated_successfully_m'));
            }

            return to_route(self::LIST_ROUTE)
                ->with('success', trans_choice('custom.nomenclature.municipalities', 1)." ".__('messages.created_successfully_m'));
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
        $qItem = EkatteMunicipality::query();
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
