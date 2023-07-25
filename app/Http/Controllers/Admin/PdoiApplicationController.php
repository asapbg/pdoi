<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApplicationEventsEnum;
use App\Enums\MailTemplateTypesEnum;
use App\Enums\PdoiApplicationStatusesEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminCreateApplicationRequest;
use App\Http\Requests\ApplicationRenewRequest;
use App\Http\Requests\RegisterEventForwardRequest;
use App\Http\Requests\RegisterEventRequest;
use App\Models\Category;
use App\Models\Country;
use App\Models\EkatteArea;
use App\Models\EkatteMunicipality;
use App\Models\EkatteSettlement;
use App\Models\Event;
use App\Models\File;
use App\Models\MailTemplates;
use App\Models\PdoiApplication;
use App\Models\PdoiResponseSubject;
use App\Models\ProfileType;
use App\Models\ReasonRefusal;
use App\Services\ApplicationService;
use App\Services\FileOcr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        if( isset($requestFilter['search']) ) {
            $paginate = $filter['paginate'] ?? PdoiApplication::PAGINATE;
            $items = PdoiApplication::with(['responseSubject', 'responseSubject.translations', 'parent'])
                ->FilterBy($requestFilter)
                ->ByUserSubjects()
                ->orderBy('id', 'desc')
                ->orderBy('parent_id', 'asc')
                ->paginate($paginate);
        }
        $listRouteName = 'admin.application';
        return $this->view('admin.applications.index', compact('items', 'filter', 'listRouteName'));
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

        return $this->view('admin.applications.view', compact('item', 'categories'));
    }

    public function create(Request $request)
    {
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
                return redirect(route('admin.application'))->with('success', __('messages.created_successfully_m'));
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
            , 'areas', 'municipality', 'settlements', 'subjects'));
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
        if( !$user->canAny(['update'], $item) ){
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
        if( !$user->canAny(['update'], $item) ){
            return response()->json(['error' => 1, 'message' => __('messages.unauthorized')]);
        }

        $item->categories()->detach($request->input('category'));

        return response()->json(['success' => 1, Response::HTTP_OK]);
    }

    public function newEvent(Request $request, int $applicationId = 0, int $eventId = 0)
    {
        $user = auth()->user();

        $application = PdoiApplication::find($applicationId);
        if( !$application || !$user->canAny(['update'], $application) ){
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

            return $this->view('admin.applications.'.$view, compact('application', 'event', 'subjects', 'newEndDate', 'mailTemplate', 'refusalReasons'));
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
        $validator = Validator::make($request->all(), $eventRequest->rules());
        if( $validator->fails() ) {
            return back()->withInput()->withErrors($validator->errors());
        }

        $validated = $validator->validated();
        $user = auth()->user();

        $application = PdoiApplication::find($validated['application']);
        if( !$application || !$user->canAny(['update'], $application) ){
            abort(Response::HTTP_NOT_FOUND);
        }

        $appService = new ApplicationService($application);

        //If event is forward replace event with his child event
        if( $event->app_event == ApplicationEventsEnum::FORWARD->value ) {
            if( (int)$validated['in_platform'] ) {
                //TODO fix me check if new subject is child of current
                $isChild = false;
                if( $isChild ) {
                    $event = 'Препратено по компетентност към подчинен субект';
                    echo $event;exit;
                }
            } else {
                if( (int)$validated['subject_is_child'] ) {
                    $event = 'Препратено по компетентност към подчинен субект';
                    echo $event;exit;
                } else {
                    $event = 'Препратено по компетентност на субект, нерегистриран на платформата';
                    echo $event;exit;
                }
            }
        }

        if( $appService->registerEvent($event->app_event, $validated) ) {
            return redirect(route('admin.application.view', ['item' => $application->id]))->with('success', __('Успено завършено регистриране на събитие '.$event->name));
        } else {
            back()->with('danger', __('custom.system_error'));
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
            'formDate' => array(
                'type' => 'datepicker',
                'value' => $request->input('formDate'),
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
