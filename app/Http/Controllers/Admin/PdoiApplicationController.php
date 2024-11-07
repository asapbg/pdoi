<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApplicationEventsEnum;
use App\Enums\MailTemplateTypesEnum;
use App\Enums\PdoiApplicationStatusesEnum;
use App\Enums\PdoiSubjectDeliveryMethodsEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminCreateApplicationRequest;
use App\Http\Requests\ApplicationRenewRequest;
use App\Http\Requests\RegisterEventForwardRequest;
use App\Http\Requests\RegisterEventRequest;
use App\Models\Category;
use App\Models\ChangeDecisionReason;
use App\Models\Country;
use App\Models\CustomActivity;
use App\Models\CustomNotification;
use App\Models\CustomRole;
use App\Models\EkatteArea;
use App\Models\EkatteMunicipality;
use App\Models\EkatteSettlement;
use App\Models\Event;
use App\Models\File;
use App\Models\MailTemplates;
use App\Models\NoConsiderReason;
use App\Models\NotificationError;
use App\Models\PdoiApplication;
use App\Models\PdoiApplicationEvent;
use App\Models\PdoiApplicationRestoreRequest;
use App\Models\PdoiResponseSubject;
use App\Models\ProfileType;
use App\Models\ReasonRefusal;
use App\Notifications\NotifyUserForAppStatus;
use App\Services\ApplicationService;
use App\Services\FileOcr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\HttpFoundation\Response;

class PdoiApplicationController extends Controller
{
    public function index(Request $request): \Illuminate\View\View
    {
        $filter = $this->filters($request);
        $requestFilter = $request->all();
        $items = null;
//        if( isset($requestFilter['search']) ) {
            $paginate = $filter['paginate'] ?? PdoiApplication::PAGINATE;
            $items = PdoiApplication::with(['responseSubject', 'responseSubject.translations', 'parent'])
                ->FilterBy($requestFilter)
                ->ByUserSubjects()
                ->orderBy('id', 'desc')
                ->orderBy('parent_id', 'asc')
                ->paginate($paginate);
//        }
        $listRouteName = 'admin.application';
        $applicationsCnt = PdoiApplication::applicationCounter();
        return $this->view('admin.applications.index', compact('items', 'filter', 'listRouteName', 'applicationsCnt'));
    }

    public function show(Request $request, int $id = 0): \Illuminate\View\View
    {
        $item = PdoiApplication::with(['files', 'responseSubject', 'responseSubject.translations', 'events', 'events.event', 'events.event.translation', 'events.user',
            'categories', 'categories.translations', 'profileType', 'profileType.translations', 'country',
            'country.translations', 'area', 'area.translations', 'municipality', 'municipality.translations',
            'settlement', 'settlement.translations', 'currentEvent', 'currentEvent.event', 'currentEvent.event.translation', 'currentEvent.event.nextEvents',
            'currentEvent.event.nextEvents.extendTimeReason', 'currentEvent.event.nextEvents.extendTimeReason.translation'])
            ->find((int)$id);

        if( !$item ) {
            abort(Response::HTTP_NOT_FOUND);
        }
        $user = auth()->user();
        if( !$user->canAny(['update', 'view'], $item) ){
            abort(Response::HTTP_NOT_FOUND);
        }

        $categories = Category::optionsList();
        $customActivity = null;

//        if(auth()->user()->hasRole(CustomRole::SUPER_USER_ROLE)){
            $customActivity = $item->communication($request);
            //For local test
//            $customActivity = json_decode(file_get_contents("C:\Users\magdalena.mitkova\Desktop\pitay_json.json"), true);
//            $customActivity = array_map(function ($row){ return (object)$row; }, $customActivity);
//        }
        $refusalReasons = ReasonRefusal::optionsList();
        $noConsiderReasons = NoConsiderReason::optionsList();
        $event = Event::where('app_event', '=', Event::APP_EVENT_FINAL_DECISION)->first();
        return $this->view('admin.applications.view', compact('item', 'categories', 'refusalReasons', 'noConsiderReasons', 'event', 'customActivity'));
    }

    public function register(Request $request, int $id = 0)
    {
        $item = PdoiApplication::find((int)$id);

        if( !$item ) {
            abort(Response::HTTP_NOT_FOUND);
        }
        $user = auth()->user();
        if( !$user->can('register', $item) ){
            abort(Response::HTTP_NOT_FOUND);
        }

        try {
//            if(
//                $item->responseSubject->delivery_method == PdoiSubjectDeliveryMethodsEnum::SDES->value
//                || $item->responseSubject->delivery_method == PdoiSubjectDeliveryMethodsEnum::EMAIL->value
//                || $item->responseSubject->delivery_method == PdoiSubjectDeliveryMethodsEnum::SEOS->value
//            ){
//                $item->status = PdoiApplicationStatusesEnum::IN_PROCESS->value;
//                $item->registration_date = date('Y-m-d H:i:s');
//                $item->response_end_time = Carbon::now()->addDays(PdoiApplication::DAYS_AFTER_SUBJECT_REGISTRATION)->endOfDay();
//                $item->status_date = date('Y-m-d H:i:s');
//                $item->save();
//            }

            $appService = new ApplicationService($item);
            $appService->registerEvent(ApplicationEventsEnum::MANUAL_REGISTER->value);

            CustomNotification::where('data', 'like', '%"application_id":'.$item->id.',%')
                ->where('type', '=', 'App\Notifications\NotifySubjectNewApplication')
                ->where('cnt_send', '<>', CustomNotification::PDOI_APP_CNT_DISABLE_NUMBER)
                ->update(['cnt_send' => CustomNotification::PDOI_APP_CNT_DISABLE_NUMBER]);

            activity('applications')
                ->performedOn($item)
                ->event('manual_register')
                ->withProperties([
                    'user_id' => auth()->user()->id,
                    'user_name' => auth()->user()->fullName()
                ])
                ->log('manual_register');

            $item->applicant->notify(new NotifyUserForAppStatus($item));

            return redirect(route('admin.application.view', $item->id))->with('success', 'Заявлението е регситрирано успешно');
        } catch (\Exception $e){
            Log::error('Manual register application (ID '.$item->id.') error:'. $e);
            return back()->with('danger', __('messages.system_error'));
        }

    }

    public function showLog(Request $request, int|string $id, string $type)
    {
//        if(!auth()->user()->hasAnyRole([CustomRole::SUPER_USER_ROLE])){
//            return back()->with('danger', __('messages.unauthorized'));
//        }

        $view = '';
        try {
            switch ($type){
                case 'event': //pdoi_application_event
                    $event = PdoiApplicationEvent::find($id);
                    if($event){
                        $title = $event->eventReasonName;
                        $rawItem['event'] = $event;
                        $item = $rawItem;
                        $view = 'log_view_event';
                    }
                    break;
                case 'activity': //success seos, error seos, notify_moderators_for_new_app
                    $activity = CustomActivity::find((int)$id);
                    if($activity){
                        $jsonProperties = $activity->properties ?? null;
                        $rawItem['activity'] = $activity;
                        if(in_array($activity->event, ['send_to_seos', 'error_check_status_in_seos', 'success_check_status_in_seos', 'error_send_to_seos', 'success_send_to_seos'])) {
                            $view = 'log_view_activity_seos';

                            if (isset($jsonProperties['egov_message_id'])) {
                                $egovM = \App\Models\Egov\EgovMessage::find($jsonProperties['egov_message_id']);
                                $rawItem['egov_message'] = $egovM;

                                if (isset($jsonProperties['notification_id'])) {
                                    $notifcationM = \App\Models\CustomNotification::find($jsonProperties['notification_id']);
                                    $rawItem['notification'] = $notifcationM;

                                    $item = $rawItem;
                                    $title = __('custom.'.$activity->event);
                                }
                            }
                        }
                    }
                    break;
                case 'notification': //success without seos
                    $notification = CustomNotification::find($id);
                    if($notification){
                        $rawItem['notification'] = $notification;
                        if($notification->egov_message_id){
                            $rawItem['egov_message'] = \App\Models\Egov\EgovMessage::find($notification->egov_message_id);
                        }

                        $item = $rawItem;
                        $title = __('custom.notification_types.'.$notification->type);
                        $view = 'log_view_notification';
                    }
                    break;
                case 'notification_error': //error without seos
                    $notification_error = NotificationError::find((int)$id);
                    if($notification_error){
                        $rawItem['notification_error'] = $notification_error;
                        if($notification_error->notification && $notification_error->notification->egov_message_id){
                            $rawItem['egov_message'] = \App\Models\Egov\EgovMessage::find($notification_error->notification->egov_message_id);
                        }

                        $title = __('custom.notification_types.'.$notification_error->notification?->type);
                        $item = $rawItem;
                        $view = 'log_view_notification_error';
                    }
                    break;
            }
        }catch(\Exception $e){
            Log::error('Application log record view ID ('.$id.') TYPE ('.$type.') error '.$e );
        }

        if(!isset($item)){
            abort(\Illuminate\Http\Response::HTTP_NOT_FOUND);
        }

        $this->setTitleHeading('Прегелд на активност '.(isset($title) ? $title : ''));
        return $this->view('admin.applications.'.$view, compact('item'));
    }

    public function create(Request $request)
    {
        $defaultCountry = Country::isDefault()->first();
        if( $request->isMethod('post') ) {
            $appRequest = new AdminCreateApplicationRequest();
            $validator = Validator::make($request->all(), $appRequest->rules());
            if( $validator->fails() ){
                return back()->withInput()->withErrors($validator->errors());
            }

            $validated = $validator->validated();
            //TODO fix me why files are missing
            $application = new PdoiApplication();
            if(!auth()->user()->can('createManual', $application) ){
                abort(Response::HTTP_NOT_FOUND);
            }

            $validated['request'] = htmlentities(stripHtmlTags($validated['request']));

            $eventData = [
                'final_status' => $validated['status'],
                'add_text' => $validated['response'],
                'files' => $validated['files'] ?? [],
                'file_description' => $validated['file_description'] ?? [],
                'file_visible' => $validated['file_visible'] ?? [],
            ];

            foreach (['files', 'file_description', 'file_visible', 'status', 'response'] as $field) {
                if(isset($validated[$field])) { unset($validated[$field]);}
            }

            if($defaultCountry->id != $validated['country_id']){
                $validated['area_id'] = null;
                $validated['municipality_id'] = null;
                $validated['settlement_id'] = null;
            }
            DB::beginTransaction();
            try {
                $application->fill($validated);
                $application->user_reg = auth()->user()->id;
                $application->application_uri = round(microtime(true)).'-'. displayDate(Carbon::now());
                $application->response_date = Carbon::now();
                $application->status_date = Carbon::now();
                $application->manual = 1;
                $application->save();
                $application->refresh();

                $event = Event::find(ApplicationEventsEnum::FINAL_DECISION->value);
                $appService = new ApplicationService($application);
                $appService->registerEvent($event->app_event, $eventData, true);
                DB::commit();
                return redirect(route('admin.application'))->with('success', trans_choice('custom.applications', 1).' '. __('messages.created_successfully_m'));
            } catch (\Exception $e){
                DB::rollBack();
                logError('Create manual application', 'data: '.json_encode($validated).' | Error: '.$e->getMessage());
                return back()->withInput()->with('danger', __('custom.system_error'));
            }
        }

        $profileTypes = ProfileType::optionsList();
        $countries = Country::optionsList();
        $areas = EkatteArea::optionsList();
        $municipality = EkatteMunicipality::optionsList();
        $settlements = EkatteSettlement::optionsList();
        $subjects = optionsFromModel(PdoiResponseSubject::simpleOptionsList());
        return $this->view('admin.applications.create', compact('profileTypes','countries'
            , 'areas', 'municipality', 'settlements', 'subjects', 'defaultCountry'));
    }

    public function showFullHistory(Request $request, int $id = 0): \Illuminate\View\View
    {
        $item = PdoiApplication::with(['files', 'responseSubject', 'responseSubject.translations',
            'categories', 'categories.translations', 'profileType', 'profileType.translations', 'country',
            'country.translations', 'area', 'area.translations', 'municipality', 'municipality.translations',
            'settlement', 'settlement.translations', 'currentEvent', 'currentEvent.event', 'currentEvent.event.nextEvents',
            'currentEvent.event.nextEvents.extendTimeReason', 'currentEvent.event.nextEvents.extendTimeReason.translation'])
            ->find((int)$id);
        if( !$item ) {
            abort(Response::HTTP_NOT_FOUND);
        }
        $user = auth()->user();
        if( !$user->can('view', $item) ){
            abort(Response::HTTP_NOT_FOUND);
        }

        $categories = Category::optionsList();

        return $this->view('admin.applications.view_full_history', compact('item', 'categories'));
    }

    public function addCategory(Request $request): \Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        if( !$request->filled('categories') ) {
            return back()->with('danger', __('custom.category_not_selected'))->withInput();
        }
        $item = PdoiApplication::find((int)$request->input('id'));
        if( !$item ) {
            abort(Response::HTTP_NOT_FOUND);
        }
        $user = auth()->user();
        if( !$user->canAny(['updateCategory'], $item) ){
            abort(Response::HTTP_NOT_FOUND);
        }

        $item->categories()->attach($request->input('categories'));

        return redirect(route('admin.application.view', ['item' => $item->id]))
            ->with('success', __('custom.success_update'));
    }

    public function removeCategory(Request $request)
    {
        if( !$request->filled('category') ) {
            return response()->json(['error' => 1, 'message' => __('custom.category_not_selected')]);
        }
        $item = PdoiApplication::find((int)$request->input('id'));
        if( !$item ) {
            return response()->json(['error' => 1, 'message' => __('custom.record_not_found')]);
        }
        $user = auth()->user();
        if( !$user->canAny(['updateCategory'], $item) ){
            return response()->json(['error' => 1, 'message' => __('messages.unauthorized')]);
        }

        $item->categories()->detach($request->input('category'));

        return response()->json(['success' => 1, Response::HTTP_OK]);
    }

    public function newEvent(Request $request, int $applicationId = 0, int $eventId = 0)
    {
        $user = auth()->user();

        $application = PdoiApplication::find($applicationId);
        if( !$application ){
            abort(Response::HTTP_NOT_FOUND);
        }

        $policyToCheck = $application->status == PdoiApplicationStatusesEnum::NO_REVIEW->value ? 'updateExpired' : 'update';
        if($user->cannot($policyToCheck, $application)){
            abort(Response::HTTP_NOT_FOUND);
        }


        $event = Event::find($eventId);
        if( !$event ) {
            abort(Response::HTTP_NOT_FOUND);
        }

        if( in_array($event->app_event, ApplicationEventsEnum::userEvents()) ) {
            $newEndDate = null;
            if( $event->days ) {
                $newEndDate = match ($event->app_event){
                    ApplicationEventsEnum::ASK_FOR_INFO->value => displayDate(Carbon::now()->addDays($event->days)),
                    ApplicationEventsEnum::EXTEND_TERM->value => displayDate(Carbon::parse($application->response_end_time)->addDays($event->days))
                };
            }

            $view = match ($event->app_event) {
                ApplicationEventsEnum::FORWARD->value => 'new_event_forward',
                default => 'new_event',
            };
            $subjects = optionsFromModel(PdoiResponseSubject::simpleOptionsList());

            $mailTemplate = match ($event->app_event) {
                ApplicationEventsEnum::FORWARD->value => MailTemplates::where('type', MailTemplateTypesEnum::RZS_MANUAL_FORWARD->value)->first(),
                default => null,
            };

            $refusalReasons = ReasonRefusal::optionsList();
            $noConsiderReasons = NoConsiderReason::optionsList();
            $changeDecisionReasons = ChangeDecisionReason::optionsList();

            return $this->view('admin.applications.'.$view, compact('application', 'event', 'subjects', 'newEndDate', 'mailTemplate', 'refusalReasons', 'noConsiderReasons', 'changeDecisionReasons'));
        }

        $appService = new ApplicationService($application);
        if( $appService->registerEvent($event->app_event) ) {
            return redirect(route('admin.application.view', ['item' => $application->id]));
        } else {
            back()->with('danger', __('custom.system_error'));
        }
    }

    public function storeNewEvent(Request $request)
    {
        $event = Event::find((int)$request->input('event'));
        if(!$event || !in_array($event->app_event, ApplicationEventsEnum::userEvents()) ) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $eventRequest = $event->app_event == ApplicationEventsEnum::FORWARD->value ?
            new RegisterEventForwardRequest() : new RegisterEventRequest();

        $validator = Validator::make($request->all(), $eventRequest->rules(), $eventRequest->messages());
        if( $validator->fails() ) {
            return back()->withInput()->withErrors($validator->errors());
        }

        $validated = $validator->validated();

        $user = auth()->user();

        $application = PdoiApplication::find($validated['application']);

        if( !$application ){
            abort(Response::HTTP_NOT_FOUND);
        }

        $policyToCheck = $application->status == PdoiApplicationStatusesEnum::NO_REVIEW->value ? 'updateExpired' : 'update';
        if($user->cannot($policyToCheck, $application)){
            abort(Response::HTTP_NOT_FOUND);
        }

        if(isset($validated['change_decision_reasons_select']) && (int)$validated['change_decision_reasons_select'] > 0){
            $changeDecisionReason = ChangeDecisionReason::find((int)$validated['change_decision_reasons_select']);
            if($changeDecisionReason){
                $validated['edit_final_decision_reason'] = $changeDecisionReason->name;
            }
            unset($validated['change_decision_reasons_select']);
        }

        $appService = new ApplicationService($application);

        //detect if event is forward and need to switch to child event depending on user selected new subject
        if( $event->app_event == ApplicationEventsEnum::FORWARD->value ) {
            if( (int)$validated['in_platform'] ) {
                //changed many subjects
                $allSubjects = sizeof($validated['new_resp_subject_id']);
                $successSubjects = [];
                $errorSubjects = [];
                foreach ($validated['new_resp_subject_id'] as $key => $subj){
                    //if new subject is child of current
                    if( PdoiResponseSubject::isChildOf((int)$validated['old_subject'], (int)$subj) ) {
                        $event = Event::where('app_event', '=', ApplicationEventsEnum::FORWARD_TO_SUB_SUBJECT->value)->first();
                    }
                    $subEventData = $validated;
                    $subEventData['new_resp_subject_id'] = $subj;
                    unset($subEventData['event']);
                    if(!$appService->registerEvent($event->app_event, $subEventData, false, $key > 0)){
                        $errorSubjects[] = PdoiResponseSubject::find($subj)->subject_name;
                    } else{
                        $successSubjects[] = PdoiResponseSubject::find($subj)->subject_name;
                    }
                }
                if(!sizeof($successSubjects)){
                    return back()->withInput()->with('danger', __('custom.system_error'));
                } else if($allSubjects != sizeof($successSubjects)){
                    return redirect(route('admin.application.view', ['item' => $application->id]))->with('warning', __('За следните Задължени субекти регистрирането на събитеие не беше успешно: '. implode(', ', $errorSubjects)));
                } else{
                    return redirect(route('admin.application.view', ['item' => $application->id]))->with('success', __('Успешно завършено регистриране на събитие Препращане по компетентност'));
                }
            } else {
                if( isset($validated['subject_is_child']) && (int)$validated['subject_is_child'] ) {
                    $event = Event::where('app_event', '=', ApplicationEventsEnum::FORWARD_TO_NOT_REGISTERED_SUB_SUBJECT->value)->first();
                    unset($validated['event']);
                } else {
                    $event = Event::where('app_event', '=', ApplicationEventsEnum::FORWARD_TO_NOT_REGISTERED_SUBJECT->value)->first();
                    unset($validated['event']);
                }
            }
        }

        if( $appService->registerEvent($event->app_event, $validated) ) {
            return redirect(route('admin.application.view', ['item' => $application->id]))->with('success', __('Успешно завършено регистриране на събитие '.$event->name));
        } else {
            return back()->withInput()->with('danger', __('custom.system_error'));
        }
    }

    public function renew(Request $request, $applicationId): \Illuminate\View\View
    {
        $user = auth()->user();

        $application = PdoiApplication::find($applicationId);
        if( !$application || !$user->canAny(['renew'], $application) ){
            abort(Response::HTTP_NOT_FOUND);
        }

        if( !PdoiApplicationStatusesEnum::canRenew((int)$application->status) ){
            abort(Response::HTTP_NOT_FOUND);
        }

        return $this->view('admin.applications.renew', compact('application'));
    }

    public function renewSubmit(ApplicationRenewRequest $request): \Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();
        $validated = $request->validated();
        $application = PdoiApplication::find((int)$validated['application']);
        if( !$application ) {
            abort(Response::HTTP_NOT_FOUND);
        }

        if( !$application || !$user->canAny(['renew'], $application) ){
            abort(Response::HTTP_NOT_FOUND);
        }

        if( !PdoiApplicationStatusesEnum::canRenew((int)$application->status) ){
            abort(Response::HTTP_NOT_FOUND);
        }

        $appService = new ApplicationService($application);
        $registerEvent = $appService->registerEvent(ApplicationEventsEnum::RENEW_PROCEDURE->value, $validated);

        $restoreRequests = $application->restoreRequests()->where('status', '=', PdoiApplicationRestoreRequest::STATUS_IN_PROCESS)->get()->first();
        if($restoreRequests){
            $restoreRequests->status = PdoiApplicationRestoreRequest::STATUS_APPROVED;
            $restoreRequests->status_datetime = databaseDateTime(Carbon::now());
            $restoreRequests->status_user_id = auth()->user()->id;
            $restoreRequests->save();
        }

        if ( is_null($registerEvent) ) {
            return back()->withInput()->with('danger', __('custom.system_error'));
        }
        return to_route('admin.application.view', ['item' => $application->id])
            ->with('success', trans_choice('custom.applications', 1)." ".__('messages.updated_successfully_n'));
    }

    private function filters($request): array
    {
        return array(
            'period' => array(
                'type' => 'select',
                'options' => optionsTimePeriod(true,'', __('custom.period')),
                'default' => '',
                'value' => $request->input('period'),
                'col' => 'col-md-3'
            ),
            'fromDate' => array(
                'type' => 'datepicker',
                'value' => $request->input('fromDate'),
                'placeholder' => __('custom.begin_date'),
                'col' => 'col-md-2'
            ),
            'toDate' => array(
                'type' => 'datepicker',
                'value' => $request->input('toDate'),
                'placeholder' => __('custom.end_date'),
                'col' => 'col-md-2'
            ),
            'expired' => array(
                'type' => 'checkbox',
                'label' => __('custom.expired_term'),
                'value' => 1,
                'checked' => (int)$request->input('expired'),
                'col' => 'col-md-3'
            ),
            'status' => array(
                'type' => 'select',
                'options' => optionsApplicationStatus(true, '', __('custom.status')),
                'default' => '',
                'value' => $request->input('status'),
                'col' => 'col-md-4'
            ),
            'applicationUri' => array(
                'type' => 'text',
                'placeholder' => __('custom.reg_number'),
                'value' => $request->input('applicationUri'),
                'col' => 'col-md-4'
            ),
            'category' => array(
                'type' => 'select',
                'options' => optionsFromModel(Category::optionsList(), true,'', trans_choice('custom.categories',1)),
                'default' => '',
                'value' => $request->input('category'),
                'col' => 'col-md-4'
            ),
            'text' => array(
                    'type' => 'text',
                    'placeholder' => __('validation.attributes.text'),
                    'value' => $request->input('text')
            ),
            'fileContent' => array(
                    'type' => 'text',
                    'placeholder' => __('custom.file_content'),
                    'value' => $request->input('fileContent')
            ),
            'subjects' => array(
                'type' => 'subjects',
                'multiple' => false,
                'options' => optionsFromModel(PdoiResponseSubject::simpleOptionsList(), true, '', trans_choice('custom.pdoi_response_subjects', 1)),
                'value' => $request->input('subjects'),
                'default' => '',
                'placeholder' => trans_choice('custom.pdoi_response_subjects',1),
                'col' => 'col-md-9'
            )
        );
    }
}
