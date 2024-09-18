<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RejectRenewRequest;
use App\Mail\ModeratorExpareApplication;
use App\Mail\UserRejectRenewRequest;
use App\Models\PdoiApplicationRestoreRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PdoiApplicationRestoreRequestController extends AdminController
{
    const LIST_ROUTE = 'admin.restore_requests';
    const EDIT_ROUTE = 'admin.restore_requests.edit';
    const STORE_ROUTE = 'admin.restore_requests.store';
    const LIST_VIEW = 'admin.restore_requests.index';
    const EDIT_VIEW = 'admin.restore_requests.edit';
    const PREVIEW_VIEW = 'admin.restore_requests.show';

    public function index(Request $request)
    {
        $requestFilter = $request->all();
        $filter = $this->filters($request);
        $paginate = $filter['paginate'] ?? PdoiApplicationRestoreRequest::PAGINATE;

        $items = PdoiApplicationRestoreRequest::with(['application'])
            ->whereHas('application', function ($q){
                $q->ByUserSubjects();
            })
            ->FilterBy($requestFilter)
            ->paginate($paginate);

        $editRouteName = self::EDIT_ROUTE;
        $listRouteName = self::LIST_ROUTE;

        return $this->view(self::LIST_VIEW, compact('filter', 'items', 'editRouteName', 'listRouteName'));
    }

    /**
     * @param Request $request
     * @param PdoiApplicationRestoreRequest $item
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, PdoiApplicationRestoreRequest $item)
    {
        if( $request->user()->cannot('update', $item) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }

        $listRouteName = self::LIST_ROUTE;
        return $this->view(self::EDIT_VIEW, compact('item', 'listRouteName'));
    }

    /**
     * @param Request $request
     * @param PdoiApplicationRestoreRequest $item
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(Request $request, PdoiApplicationRestoreRequest $item)
    {
        if( $request->user()->can('update', $item) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }

        $listRouteName = self::LIST_ROUTE;
        return $this->view(self::PREVIEW_VIEW, compact('item', 'listRouteName'));
    }

    public function reject(RejectRenewRequest $request)
    {
        $validated = $request->validated();
        $item = PdoiApplicationRestoreRequest::find($validated['id']);
        if( $request->user()->cannot('update', $item) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }

        DB::beginTransaction();
        try {
            $item->status = PdoiApplicationRestoreRequest::STATUS_REGECTED;
            $item->status_datetime = databaseDateTime(Carbon::now());
            $item->reason_refuse = $validated['answer'];
            $item->status_user_id = auth()->user()->id;
            $item->save();
            DB::commit();

            if($item->author){
                $email = config('app.env') != 'production' ? config('mail.local_to_mail') : $item->author->email;
                Mail::to($email)->send(new UserRejectRenewRequest($item, $item->author));
            }
            return redirect(route('admin.restore_requests'))->with('success', 'Заявката за възобновяване беше отказана успешно');
        } catch (\Exception $e){
            DB::rollBack();
            logError('Apply application (front)', $e->getMessage());
            return response()->json(['errors' => __('custom.system_error')], 200);
        }

    }

    private function filters($request)
    {
        return array();
    }
}
