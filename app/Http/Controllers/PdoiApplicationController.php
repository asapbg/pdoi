<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationEventsEnum;
use App\Enums\MailTemplateTypesEnum;
use App\Enums\PdoiApplicationStatusesEnum;
use App\Enums\PdoiSubjectDeliveryMethodsEnum;
use App\Http\Requests\PdoiApplicationApplyRequest;
use App\Http\Requests\RenewApplicationStoreRequest;
use App\Http\Requests\StoreInfoEventRequest;
use App\Http\Resources\PdoiApplicationResource;
use App\Http\Resources\PdoiApplicationShortCollection;
use App\Http\Resources\PdoiApplicationShortResource;
use App\Mail\ModeratorNewApplication;
use App\Models\Category;
use App\Models\Country;
use App\Models\EkatteArea;
use App\Models\EkatteMunicipality;
use App\Models\EkatteSettlement;
use App\Models\Event;
use App\Models\File;
use App\Models\MailTemplates;
use App\Models\PdoiApplication;
use App\Models\PdoiApplicationRestoreRequest;
use App\Models\PdoiResponseSubject;
use App\Models\User;
use App\Notifications\NotifySubjectNewApplication;
use App\Notifications\NotifyUserForAppStatus;
use App\Services\ApplicationService;
use App\Services\FileOcr;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PdoiApplicationController extends Controller
{
    //public page
    public function index(Request $request)
    {
        $filter = $this->filters($request);
        $requestFilter = $request->all();
        $applications = null;
        $sort = $request->filled('sort') ? $request->input('sort') : 'apply_date';
        $sortOrd = $request->filled('ord') ? $request->input('ord') : 'desc';
        if( isset($requestFilter['search']) ) {
            $paginate = $filter['paginate'] ?? PdoiApplication::PAGINATE;
            $appQ = PdoiApplication::with(['responseSubject', 'responseSubject.translation',
                'events', 'events.user', 'events.event', 'events.event.translation',
                'lastFinalEvent', 'lastFinalEvent.visibleFiles', 'children'])
                ->FilterBy($request->all())
                ->SortedBy($sort,$sortOrd);
            $applications = (new PdoiApplicationShortCollection($appQ->paginate($paginate)))->resolve();
        }
        $applicationsCnt = PdoiApplication::applicationCounter();
        $titlePage =__('custom.searching');
        $this->setBreadcrumbsTitle($titlePage);
        return $this->view('front.application.list', compact('applications', 'titlePage', 'filter', 'applicationsCnt'));
    }

    public function show(Request $request, int $id = 0)
    {
        $item = $this->getFullRecord($id);
        if( !$item ) {
            abort(Response::HTTP_NOT_FOUND);
        }
        $item->number_of_visits += 1;
        $item->save();
        $application = (new PdoiApplicationShortResource($item))->resolve();
        $this->setTitleSingular(trans_choice('custom.applications', 1));
        return $this->view('front.application.view', compact('application'));
    }

    public function myApplications(Request $request)
    {
        $filter = $this->filters($request);
        $paginate = $request->filled('paginate') ? $request->get('paginate') : PdoiApplication::PAGINATE;
        $sort = $request->filled('sort') ? $request->input('sort') : 'apply_date';
        $sortOrd = $request->filled('ord') ? $request->input('ord') : 'desc';

        $appQ = PdoiApplication::with(['responseSubject', 'responseSubject.translation' ,
            'events', 'events.user', 'events.event', 'events.event.translation',
            'lastFinalEvent', 'lastFinalEvent.visibleFiles'])
            ->FilterBy($request->all())
            ->SortedBy($sort,$sortOrd)
            ->where('user_reg', $request->user()->id)
            ->where('manual', '=', 0);
        $applications = (new PdoiApplicationShortCollection($appQ->paginate($paginate)))->resolve();
        $myList = true;
        $titlePage =__('front.my_application.title');
        return $this->view('front.application.list', compact('applications', 'titlePage', 'myList', 'filter'));
    }

    public function showMy(Request $request, int $id = 0)
    {
        $item = $this->getFullRecord((int)$id);
        if( !$item ) {
            abort(Response::HTTP_NOT_FOUND);
        }
        if( !$request->user()->can('viewMy', $item) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }

        $application = (new PdoiApplicationResource($item))->resolve();
        $application['canRenewRequest'] = $request->user()->can('renewRequest', $item);

        $lastEvent = $item->lastEvent;
        $needInfoSection = [];
        if( $lastEvent->event_type == ApplicationEventsEnum::ASK_FOR_INFO->value
            && !in_array($item->status,  PdoiApplicationStatusesEnum::finalStatuses())
            && $item->status != PdoiApplicationStatusesEnum::NO_REVIEW->value ) {
            $needInfoSection = array(
                'event_name' => $lastEvent->event->name,
                'event_date' => $lastEvent->event_date,
                'event_end' => $lastEvent->event_end_date,
                'msg' => $lastEvent->add_text,
            );
        }
        $this->setTitleSingular(trans_choice('custom.applications', 1));
        return $this->view('front.my_application.view', compact('application', 'needInfoSection'));
    }

    public function sendAdditionalInfo(StoreInfoEventRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();
        $item = PdoiApplication::find((int)$validated['item']);
        if( !$item->lastEvent || $item->lastEvent->event_type != ApplicationEventsEnum::ASK_FOR_INFO->value ) {
            abort(Response::HTTP_NOT_FOUND);
        }

        if( !$user->can('addExtraInfo', $item) ) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $appService = new ApplicationService($item);
        $appService->registerEvent(ApplicationEventsEnum::GIVE_INFO->value, ['add_text' => $validated['extra_info']]);

        return redirect(route('application.my.show', ['id' => $item->id]))->with('success', __('front.info_is_send'));
    }

    public function showMyFullHistory(Request $request, int $id = 0)
    {
        $item = $this->getFullRecord((int)$id);
        if( !$item ) {
            abort(Response::HTTP_NOT_FOUND);
        }
        if( !$request->user()->can('viewMy', $item) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }

        $application = (new PdoiApplicationResource($item))->resolve();
        return $this->view('front.my_application.view_full_history', compact('application'));
    }

    public function create(Request $request)
    {
        $user = User::with(['country', 'country.translation', 'area', 'area.translation',
            'municipality', 'municipality.translation', 'settlement', 'settlement.translation'])->find((int)$request->user()->id);

        if( !$user || $user->cannot('create', PdoiApplication::class) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }
        $title = __('front.application.title.apply');

        $defaultCountry = Country::isDefault()->first();
        $data=[];
        if( !$user->country ) {
            $data['countries'] = Country::optionsList();
        }

        $data['areas'] = EkatteArea::optionsList();
        $data['municipality'] = EkatteMunicipality::optionsList();
        $data['settlements'] = EkatteSettlement::optionsList();

        $rzs = PdoiResponseSubject::optionsList([], false, true);
        return $this->view('front.application.apply', compact('data', 'user', 'rzs', 'title', 'defaultCountry'));
    }

    public function store(Request $request) {

        $user = $request->user();
        if( !$user || $user->cannot('create', PdoiApplication::class) ) {
            return response()->json(['errors' => __('messages.unauthorized')], 200);
        }

        $applicationRequest = new PdoiApplicationApplyRequest();
        $validator = Validator::make($request->all(), $applicationRequest->rules());
        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 200);
        }

        DB::beginTransaction();
        try {
            $validated = $validator->validated();
            $data = array('applicationsInfo' => []);
            $this->updateProfile($validated);

            foreach ($validated['subjects'] as $response_subject_id) {
                $newApplication = new PdoiApplication([
                    'user_reg' => $user->id,
                    'applicant_type' => $user->legal_form,
                    'applicant_identity' => $user->identity,
                    'email' => $user->email,
                    'post_code' => $user->post_code,
                    'full_names' => $user->names,
                    //TODO ask what is this column
                    //'headoffice' => '',
                    'country_id' => $user->country_id,
                    'area_id' => $user->ekatte_area_id,
                    'municipality_id' => $user->ekatte_municipality_id,
                    'settlement_id' => $user->ekatte_settlement_id,
                    'address' => $user->address,
                    'address_second' => $user->address_second,
                    'phone' => $user->phone,
                    'response_subject_id' => (int)$response_subject_id,
                    'request' => htmlentities(stripHtmlTags($validated['request'])),
                    'status' => PdoiApplicationStatusesEnum::RECEIVED->value,
                    'application_uri' => round(microtime(true)).'-'. displayDate(Carbon::now()),
                    'email_publication' => $validated['email_publication'] ?? 0,
                    'names_publication' => $validated['names_publication'] ?? 0,
                    'address_publication' => $validated['address_publication'] ?? 0,
                    'user_attached_files' => isset($validated['files']) ? sizeof($validated['files']) : 0,
                    'phone_publication' => $validated['phone_publication'] ?? 0,
                    'profile_type' => $user->profile_type
                    //TODO ask what is this column
//                    'headoffice_publication' => $validated['headoffice_publication'] ?? 0,
                ]);
                $newApplication->save();

                //Save user attached files
                if( isset($validated['files']) && sizeof($validated['files']) ) {
                    foreach ($validated['files'] as $key => $file) {
                        $fileNameToStore = ($key + 1).'_'.round(microtime(true)).'.'.$file->getClientOriginalExtension();
                        // Upload File
                        $file->storeAs($newApplication->fileFolder, $fileNameToStore, 'local');
                        $newFile = new File([
                            'code_object' => File::CODE_OBJ_APPLICATION,
                            'filename' => $fileNameToStore,
                            'content_type' => $file->getClientMimeType() != 'application/octet-stream' ? $file->getClientMimeType() : $file->getMimeType(),
                            'path' => $newApplication->fileFolder.$fileNameToStore,
                            'description' => $validated['file_description'][$key],
                            'user_reg' => $user->id,
                        ]);
                        $newApplication->files()->save($newFile);
                        $ocr = new FileOcr($newFile->refresh());
                        $ocr->extractText();
                    }
                }

                $subject = $newApplication->responseSubject;
                //Communication: notify subject for new application
                $instructionData = array(
                    'reg_number' => $newApplication->application_uri,
                    'date_apply' => displayDate($newApplication->created_at),
                    'administration' => $newApplication->responseSubject->subject_name,
                    'applicant' => $newApplication->full_names,
                );
                $instructionTemplate = MailTemplates::where('type', '=', MailTemplateTypesEnum::RZS_AUTO_FORWARD->value)->first();
                $message = $instructionTemplate ? Lang::get($instructionTemplate->content, $instructionData) : '';
                $notifyData['message'] = htmlentities($message);

//                //TODO fix me simulation remove after communication is ready. For now we simulate approve by RKS (деловодна система)
//                $lastNotify = DB::table('notifications')
//                    ->where('type', 'App\Notifications\NotifySubjectNewApplication')
//                    ->latest()->limit(1)->get()->pluck('id');
//                if(isset($lastNotify[0])) {
//                    $appService = new ApplicationService($newApplication);
//                    $appService->communicationCallback($lastNotify[0]);
//                }
                $appService = new ApplicationService($newApplication);
                $appService->generatePdf($newApplication);

                //Register event: register first app event
                $receivedEvent = $appService->registerEvent(ApplicationEventsEnum::SEND->value);

                if ( is_null($receivedEvent) ) {
                    DB::rollBack();
                    logError('Apply application (front): ', 'Operation roll back because cant\'t register '.ApplicationEventsEnum::SEND->name. ' event');
                    return response()->json(['errors' => __('custom.system_error')], 200);
                }
                $newApplication->refresh();
                $subject->notify(new NotifySubjectNewApplication($newApplication, $notifyData));

                $emailList = $subject->getModeratorsEmail();
                if( sizeof($emailList) ) {
                    Mail::to($emailList)->send(new ModeratorNewApplication(route('admin.application.view', ['item' => $newApplication->id])));
                }

                //return info for each generated application
                $data['applicationsInfo'][] = array(
                    'reg_number' => $newApplication->application_uri,
                    'response_subject' => $newApplication->responseSubject->subject_name,
                    'status' => $newApplication->statusName,
                    'status_date' => displayDate($newApplication->status_date),
                    'response_end_time' => displayDate($newApplication->response_end_time),
                );

                sleep(1);
            }

            DB::commit();
            $data['html'] = view('front.application.apply_result', $data)->render();
            return response()->json($data, 200);

        } catch (\Exception $e) {
            DB::rollBack();
            logError('Apply application (front)', $e->getMessage());
            return response()->json(['errors' => __('custom.system_error')], 200);
        }
    }

    public function renewMy(Request $request, int $id = 0)
    {
        $item = PdoiApplication::find($id);
        if( !$item ) {
            abort(Response::HTTP_NOT_FOUND);
        }
        if( !$request->user()->can('renewRequest', $item) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }
        $application = (new PdoiApplicationResource($item))->resolve();
        $this->setTitleSingular(__('custom.application.renew'));
        return $this->view('front.my_application.renew', compact('application'));
    }

    public function storeRenewMy(Request $request)
    {
        $applicationRequest = new RenewApplicationStoreRequest();

        $validator = Validator::make($request->all(), $applicationRequest->rules());

        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 200);
        }
        $validated = $validator->validated();

        $item = PdoiApplication::find($validated['id']);
        if( !$item ) {
            abort(Response::HTTP_NOT_FOUND);
        }
        if( !$request->user()->can('renewRequest', $item) ) {
            return response()->json(['errors' => __('messages.unauthorized')], 200);
        }

        DB::beginTransaction();
        try {

            $renew = new PdoiApplicationRestoreRequest([
                'pdoi_application_id' => $item->id,
                'applicant_id' => auth()->user()->id,
                'user_request' => $validated['request_summernote'],
                'status_datetime' => databaseDateTime(Carbon::now()),
            ]);
            $renew->save();
            //Save user attached files
            if( isset($validated['files']) && sizeof($validated['files']) ) {
                foreach ($validated['files'] as $key => $file) {
                    $fileNameToStore = ($key + 1).'_'.round(microtime(true)).'.'.$file->getClientOriginalExtension();
                    // Upload File
                    $file->storeAs($item->fileFolder, $fileNameToStore, 'local');
                    $newFile = new File([
                        'code_object' => File::CODE_OBJ_APPLICATION_RENEW,
                        'filename' => $fileNameToStore,
                        'content_type' => $file->getClientMimeType() != 'application/octet-stream' ? $file->getClientMimeType() : $file->getMimeType(),
                        'path' => $item->fileFolder.$fileNameToStore,
                        'description' => $validated['file_description'][$key],
                        'user_reg' => auth()->user()->id,
                    ]);
                    $renew->files()->save($newFile);
                    //$ocr = new FileOcr($newFile->refresh());
                    //$ocr->extractText();
                }
            }
            DB::commit();
            return response()->json(['redirect_url' => route('application.my.show', $item).'#renews'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            logError('Renew application ID'.$item->id.'  (front)', $e->getMessage());
            return response()->json(['errors' => __('custom.system_error')], 200);
        }

    }

    private function updateProfile($validatedFields) {
        $fields = User::prepareModelFields($validatedFields, true);
        $user = auth()->user();
        $user->fill($fields);
        $user->save();
    }

    private function filters($request): array
    {
        return array(
            'period' => array(
                'type' => 'select',
                'options' => optionsTimePeriod(true,'', __('custom.period')),
                'default' => '',
                'value' => $request->input('period'),
                'placeholder' => __('custom.period'),
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
            'status' => array(
                'type' => 'select',
                'options' => optionsApplicationStatus(true, '', __('custom.status')),
                'default' => '',
                'value' => $request->input('status'),
                'placeholder' => __('custom.status'),
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
                'placeholder' => trans_choice('custom.categories',1),
                'col' => 'col-md-4'
            ),
            'text' => array(
                'type' => 'text',
                'placeholder' => __('validation.attributes.text'),
                'value' => $request->input('text')
            ),
            'subjects' => array(
                'type' => 'subjects',
                'multiple' => false,
                'options' => optionsFromModel(PdoiResponseSubject::simpleOptionsList(), true,''),
                'value' => $request->input('subjects') ?? [],
                'default' => '',
                'placeholder' => trans_choice('custom.pdoi_response_subjects',1),
                'col' => 'col-md-9'
            )

        );
    }

    private function getFullRecord($id): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Builder|array|null
    {
        return PdoiApplication::with(['files', 'responseSubject', 'responseSubject.translations',
            'categories', 'categories.translations', 'profileType', 'profileType.translations', 'country',
            'country.translations', 'area', 'area.translations', 'municipality', 'municipality.translations',
            'settlement', 'settlement.translations', 'events', 'events.user', 'events.event', 'events.files', 'events.event.translation', 'lastFinalEvent', 'lastFinalEvent.visibleFiles'])
            ->find((int)$id);
    }
}
