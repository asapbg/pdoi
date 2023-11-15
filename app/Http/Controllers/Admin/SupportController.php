<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PdoiSubjectDeliveryMethodsEnum;
use App\Http\Controllers\Controller;
use App\Models\Egov\EgovMessage;
use App\Models\Page;
use App\Models\PdoiResponseSubject;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class SupportController extends Controller
{
    public function notifications(Request $request)
    {
        if(!auth()->user()->hasRole([\App\Models\CustomRole::SUPER_USER_ROLE])){
            abort(403);
        }
        $application = $request->filled('application') && !empty($request->input('application')) ? $request->input('application') : null;
        $email = $request->filled('email') && !empty($request->input('email')) ? $request->input('email') : null;
        //        dd($application, $email);
        $q = DB::table('notifications');
        if( $application ) {
            $q->whereRaw('data like \'%application_id":'.$application.'%\'');
        }
        if( $email ) {
            $q->whereRaw('data like \'%to_email":"'.$email.'%\'');
        }

        $items = $q->orderBy('created_at')
            ->paginate(20);
        $subjects = optionsFromModel(PdoiResponseSubject::simpleOptionsList(), true, '', trans_choice('custom.pdoi_response_subjects', 1));
        $filter = [
            'application' => $application,
            'email' => $email,
        ];
        $this->setBreadcrumbsTitle('Известия');

        return $this->view('admin.support.notifications', compact('items', 'subjects', 'filter'));
    }

    public function notificationView(Request $request, $id)
    {
        if(!auth()->user()->hasRole([\App\Models\CustomRole::SUPER_USER_ROLE])){
            abort(403);
        }

        $item = DB::table('notifications')
            ->where('id', '=', $id)
            ->first();
        if( !$item ) {
            abort(404);
        }
        $msgErrors = DB::table('notification_error')
            ->where('notification_id', '=', $item->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $egovMessage = null;
        if( $item->type_channel == PdoiSubjectDeliveryMethodsEnum::SDES->value ) {
            $egovMessage = EgovMessage::with(['sender', 'recipient', 'recipient.services'])->find($item->egov_message_id);
        }
        $this->setBreadcrumbsTitle('Преглед известие');
        return $this->view('admin.support.notifications_view', compact('item', 'msgErrors', 'egovMessage'));
    }
}
