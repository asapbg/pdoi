<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomNotificationStoreRequest;
use App\Models\CustomNotification;
use App\Models\User;
use App\Notifications\CustomInternalNotification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class NotificationsController extends AdminController
{
    const LIST_ROUTE = 'admin.notifications';
    const EDIT_ROUTE = 'admin.notifications.create';
    const STORE_ROUTE = 'admin.notifications.store';
    const LIST_VIEW = 'admin.notifications.index';
    const EDIT_VIEW = 'admin.notifications.create';
    const PREVIEW_VIEW = 'admin.restore_requests.show';

    public function index(Request $request)
    {
        $requestFilter = $request->all();
        $filter = $this->filters($request);
        $paginate = $filter['paginate'] ?? 20;

        $items = CustomNotification::InternalCommunication()->orderBy('created_at', 'desc')->paginate($paginate);

        $editRouteName = self::EDIT_ROUTE;
        $listRouteName = self::LIST_ROUTE;
        $title_singular = 'Създаване на ново съобщение';
        return $this->view(self::LIST_VIEW, compact('filter', 'items', 'editRouteName', 'listRouteName', 'title_singular'));
    }

    public function create(Request $request)
    {
        if( $request->user()->cannot('manage.*') ) {
            return back()->with('warning', __('messages.unauthorized'));
        }
        $recipients = User::with(['responseSubject', 'roles'])->Internal()->IsActive()->get();
        $listRouteName = self::LIST_ROUTE;
        return $this->view(self::EDIT_VIEW, compact('listRouteName', 'recipients'));
    }

    public function store(CustomNotificationStoreRequest $request)
    {
        if( $request->user()->cannot('manage.*') ) {
            return back()->with('warning', __('messages.unauthorized'));
        }
        $validated = $request->validated();

        $users = isset($validated['all']) ? User::Internal()->IsActive()->get() : User::whereIn('id', $validated['users'])->get();

        if(!$users->count()){
            return back()->withInput()->with('danger', 'Не са открити посочените получатели');
        }

        $errors = [];
        foreach ($users as $u){
            try {
                $u->notify(new CustomInternalNotification([
                    'msg' => $validated['msg']
                    , 'subject' => $validated['subject']
                    , 'sender' => auth()->user()
                    , 'internalMsg' => isset($validated['db'])
                    , 'mailMsg' => isset($validated['mail'])
                ]));
            } catch (\Exception $e){
                Log::error('Error sending message to user ID('.$u->id.'): '.$e);
                $errors[] = $u->fullName();
            }
        }

        if(!sizeof($errors)){
            return redirect(route('admin.notifications'))->with('success', 'Всички съобщения са изпратени успешно');
        } else if (sizeof($errors) == sizeof($validated['users'])){
            return back()->withInput()->with('danger', 'Възникна грешка, съобщението не е изпратено');
        } else {
            return redirect(route('admin.notifications'))->with('warning', 'Следните потребители може да не са получили съобщението: '. implode(', ', $errors));
        }
    }

    public function show(Request $request, $id): \Illuminate\View\View
    {
        $notification = CustomNotification::find($id);

        if(!$notification){
            abort(Response::HTTP_NOT_FOUND);
        }
        return $this->view('admin.notifications.show', compact('notification'));
    }

    private function filters($request)
    {
        return array();
    }
}
