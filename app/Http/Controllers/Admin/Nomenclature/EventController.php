<?php

namespace App\Http\Controllers\Admin\Nomenclature;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Requests\EventStoreRequest;
use App\Models\Event;
use App\Models\ExtendTermsReason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class EventController extends AdminController
{
    const LIST_ROUTE = 'admin.nomenclature.event';
    const EDIT_ROUTE = 'admin.nomenclature.event.edit';
    const STORE_ROUTE = 'admin.nomenclature.event.store';
    const LIST_VIEW = 'admin.nomenclatures.event.index';
    const EDIT_VIEW = 'admin.nomenclatures.event.edit';

    public function index(Request $request)
    {
        $requestFilter = $request->all();
        $filter = $this->filters($request);
        $paginate = $filter['paginate'] ?? Event::PAGINATE;

        if( !isset($requestFilter['active']) ) {
            $requestFilter['active'] = 1;
        }
        $items = Event::with(['translation'])
            ->FilterBy($requestFilter)
            ->orderByTranslation('name')
            ->paginate($paginate);
        $editRouteName = self::EDIT_ROUTE;
        $listRouteName = self::LIST_ROUTE;

        return $this->view(self::LIST_VIEW, compact('filter', 'items', 'editRouteName', 'listRouteName'));
    }

    /**
     * @param Request $request
     * @param Event $item
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, Event $item)
    {
        if( ($item && $request->user()->cannot('update', $item)) || $request->user()->cannot('create', Event::class) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }
        $storeRouteName = self::STORE_ROUTE;
        $listRouteName = self::LIST_ROUTE;
        $translatableFields = Event::translationFieldsProperties();
        $extendReasons = ExtendTermsReason::optionsList();

        $title = $item->id > 0 ? __('custom.edit_object', ['object' => trans_choice('custom.events', 1), 'object_name' => $item->name]) : __('custom.create_object', ['object' => trans_choice('custom.events', 1)]);
        $this->setTitlePlural($title);
        $this->setBreadcrumbsTitle($title);

        return $this->view(self::EDIT_VIEW, compact('item', 'storeRouteName', 'listRouteName',
            'translatableFields', 'extendReasons'));
    }

    public function store(EventStoreRequest $request, Event $item)
    {
        $id = $item->id;
        $validated = $request->validated();
        if( ($id && $request->user()->cannot('update', $item))
            || $request->user()->cannot('create', Event::class) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }

        try {
            $fillable = $this->getFillableValidated($validated, $item);
            $item->fill($fillable);
            $item->save();
            $this->storeTranslateOrNew(Event::TRANSLATABLE_FIELDS, $item, $validated);

            if( $id ) {
                return redirect(route(self::EDIT_ROUTE, $item) )
                    ->with('success', trans_choice('custom.nomenclature.event', 1)." ".__('messages.updated_successfully_m'));
            }

            return to_route(self::LIST_ROUTE)
                ->with('success', trans_choice('custom.nomenclature.event', 1)." ".__('messages.created_successfully_m'));
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->withInput(request()->all())->with('danger', __('messages.system_error'));
        }

    }

    private function filters($request)
    {
        return array();
    }

    /**
     * @param $id
     * @param array $with
     */
    private function getRecord($id, array $with = []): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Builder|array|null
    {
        $qItem = Event::query();
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
