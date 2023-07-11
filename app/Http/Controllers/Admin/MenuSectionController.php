<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MenuSectionStoreRequest;
use App\Models\MenuSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class MenuSectionController  extends AdminController
{
    const LIST_ROUTE = 'admin.menu_section';
    const EDIT_ROUTE = 'admin.menu_section.edit';
    const STORE_ROUTE = 'admin.menu_section.store';
    const LIST_VIEW = 'admin.menu_section.index';
    const EDIT_VIEW = 'admin.menu_section.edit';

    public function index(Request $request)
    {
        $requestFilter = $request->all();
        $filter = $this->filters($request);
        $paginate = $filter['paginate'] ?? MenuSection::PAGINATE;

        if( !isset($requestFilter['active']) ) {
            $requestFilter['active'] = 1;
        }
        $items = MenuSection::with(['translation', 'parent', 'parent.translation'])
            ->FilterBy($requestFilter)
            ->orderByTranslation('name')
            ->paginate($paginate);
        $toggleBooleanModel = 'MenuSection';
        $editRouteName = self::EDIT_ROUTE;
        $listRouteName = self::LIST_ROUTE;

        return $this->view(self::LIST_VIEW, compact('filter', 'items', 'toggleBooleanModel', 'editRouteName', 'listRouteName'));
    }

    /**
     * @param Request $request
     * @param MenuSection $item
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, MenuSection $item)
    {
        if( ($item && $request->user()->cannot('update', $item)) || $request->user()->cannot('create', MenuSection::class) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }
        $storeRouteName = self::STORE_ROUTE;
        $listRouteName = self::LIST_ROUTE;
        $translatableFields = MenuSection::translationFieldsProperties();
        $sections = MenuSection::optionsList((int)$item->id, true);
        return $this->view(self::EDIT_VIEW, compact('item', 'storeRouteName', 'listRouteName', 'translatableFields', 'sections'));
    }

    public function store(MenuSectionStoreRequest $request, MenuSection $item)
    {

        $id = $item->id;
        $validated = $request->validated();
        if( ($id && $request->user()->cannot('update', $item))
            || $request->user()->cannot('create', MenuSection::class) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }

        try {
            if( empty($validated['slug']) ) {
                $validated['slug'] = Str::slug($validated['name_bg']);
            }

            $validated['parent_id'] = $validated['section'];
            $fillable = $this->getFillableValidated($validated, $item);

            $level = 1;
            if( isset($fillable['parent_id']) && (int)$fillable['parent_id'] ) {
                $section = MenuSection::find((int)$fillable['parent_id']);
                if($section) {
                    $level = $section->level + 1;
                }
            }

            $item->fill($fillable);
            $item->level = $level;
            $item->save();
            $this->storeTranslateOrNew(MenuSection::TRANSLATABLE_FIELDS, $item, $validated);

            if( $id ) {
                return redirect(route(self::EDIT_ROUTE, $item) )
                    ->with('success', trans_choice('custom.menu_sections', 1)." ".__('messages.updated_successfully_m'));
            }

            //Clear menu cache
            foreach (config('available_languages') as $locale) {
                Cache::forget('menu_'.$locale['code']);
            }
            return to_route(self::LIST_ROUTE)
                ->with('success', trans_choice('custom.menu_sections', 1)." ".__('messages.created_successfully_m'));
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
        $qItem = MenuSection::query();
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
