<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MailTemplatesStoreRequest;
use App\Models\MailTemplates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class MailTemplatesController extends AdminController
{
    const LIST_ROUTE = 'admin.mail_template';
    const EDIT_ROUTE = 'admin.mail_template.edit';
    const STORE_ROUTE = 'admin.mail_template.store';
    const LIST_VIEW = 'admin.mail_template.index';
    const EDIT_VIEW = 'admin.mail_template.edit';

    public function index(Request $request)
    {
        $requestFilter = $request->all();
        $filter = $this->filters($request);
        $paginate = $filter['paginate'] ?? MailTemplates::PAGINATE;

        $items = MailTemplates::FilterBy($requestFilter)
            ->orderBy('name', 'asc')
            ->paginate($paginate);
        $editRouteName = self::EDIT_ROUTE;
        $listRouteName = self::LIST_ROUTE;

        return $this->view(self::LIST_VIEW, compact('filter', 'items', 'editRouteName', 'listRouteName'));
    }

    /**
     * @param Request $request
     * @param MailTemplates $item
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, MailTemplates $item)
    {
        if( ($item && $request->user()->cannot('update', $item)) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }
        $placeholders = MailTemplates::PLACEHOLDERS;
        $storeRouteName = self::STORE_ROUTE;
        $listRouteName = self::LIST_ROUTE;
        return $this->view(self::EDIT_VIEW, compact('item', 'storeRouteName', 'listRouteName', 'placeholders'));
    }

    public function store(MailTemplatesStoreRequest $request, MailTemplates $item)
    {
        $id = $item->id;
        $validated = $request->validated();
        if( ($id && $request->user()->cannot('update', $item)) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }

        try {
            $fillable = $this->getFillableValidated($validated, $item);
            $item->fill($fillable);
            $item->save();

            return redirect(route(self::EDIT_ROUTE, $item) )
                ->with('success', trans_choice('custom.rzs_items', 1)." ".__('messages.updated_successfully_m'));
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
        $qItem = MailTemplates::query();
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
