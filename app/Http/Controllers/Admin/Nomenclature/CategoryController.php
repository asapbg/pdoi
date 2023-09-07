<?php

namespace App\Http\Controllers\Admin\Nomenclature;

use App\Exports\NomenclatureExport;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Requests\CategoryStoreRequest;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends AdminController
{
    const LIST_ROUTE = 'admin.nomenclature.category';
    const EDIT_ROUTE = 'admin.nomenclature.category.edit';
    const STORE_ROUTE = 'admin.nomenclature.category.store';
    const LIST_VIEW = 'admin.nomenclatures.category.index';
    const EDIT_VIEW = 'admin.nomenclatures.category.edit';
    const EXPORT_TYPE = 'category';

    public function index(Request $request)
    {
        $export = $request->filled('export');
        $requestFilter = $request->all();
        $filter = $this->filters($request);
        $paginate = $filter['paginate'] ?? Category::PAGINATE;

        if( !isset($requestFilter['active']) ) {
            $requestFilter['active'] = 1;
        }
        $q = Category::with(['translation'])
            ->FilterBy($requestFilter)
            ->orderByTranslation('name');

        if( $export ) {
            return $this->getData($q, self::EXPORT_TYPE);
        } else {
            $items = $q->paginate($paginate);
        }

        $toggleBooleanModel = 'Category';
        $editRouteName = self::EDIT_ROUTE;
        $listRouteName = self::LIST_ROUTE;

        return $this->view(self::LIST_VIEW, compact('filter', 'items', 'toggleBooleanModel', 'editRouteName', 'listRouteName'));
    }

    /**
     * @param Request $request
     * @param Category $item
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, Category $item)
    {
        if( ($item && $request->user()->cannot('update', $item)) || $request->user()->cannot('create', Category::class) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }
        $storeRouteName = self::STORE_ROUTE;
        $listRouteName = self::LIST_ROUTE;
        $translatableFields = Category::translationFieldsProperties();

        $title = $item->id > 0 ? __('custom.edit_object', ['object' => trans_choice('custom.categories', 1), 'object_name' => $item->name]) : __('custom.create_object', ['object' => trans_choice('custom.categories', 1)]);
        $this->setTitlePlural($title);
        $this->setBreadcrumbsTitle($title);

        return $this->view(self::EDIT_VIEW, compact('item', 'storeRouteName', 'listRouteName', 'translatableFields'));
    }

    public function store(CategoryStoreRequest $request, Category $item)
    {
        $id = $item->id;
        $validated = $request->validated();
        if( ($id && $request->user()->cannot('update', $item))
            || $request->user()->cannot('create', Category::class) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }

        try {
            $fillable = $this->getFillableValidated($validated, $item);
            $item->fill($fillable);
            $item->save();
            $this->storeTranslateOrNew(Category::TRANSLATABLE_FIELDS, $item, $validated);

            if( $id ) {
                return redirect(route(self::EDIT_ROUTE, $item) )
                    ->with('success', trans_choice('custom.nomenclature.category', 1)." ".__('messages.updated_successfully_m'));
            }

            return to_route(self::LIST_ROUTE)
                ->with('success', trans_choice('custom.nomenclature.category', 1)." ".__('messages.created_successfully_m'));
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
        $qItem = Category::query();
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
