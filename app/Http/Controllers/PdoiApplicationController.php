<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationEventsEnum;
use App\Enums\PdoiApplicationStatusesEnum;
use App\Enums\PdoiSubjectDeliveryMethodsEnum;
use App\Http\Requests\PdoiApplicationApplyRequest;
use App\Http\Resources\PdoiApplicationResource;
use App\Http\Resources\PdoiApplicationShortCollection;
use App\Http\Resources\PdoiApplicationShortResource;
use App\Mail\NotiyUserApplicationStatus;
use App\Mail\SubjectRegisterNewApplication;
use App\Models\Category;
use App\Models\Country;
use App\Models\EkatteArea;
use App\Models\EkatteMunicipality;
use App\Models\EkatteSettlement;
use App\Models\Event;
use App\Models\File;
use App\Models\PdoiApplication;
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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PdoiApplicationController extends Controller
{
    //public page
    public function index(Request $request)
    {
        $testApplication = PdoiApplication::find(10);
        $testApplication->applicant->notify(new NotifyUserForAppStatus($testApplication));
$appService = new ApplicationService($testApplication);
$appService->communicationCallback(json_encode(['notification_id' => DB::table('notifications')->latest()->limit(1)->first()->id]));

        $filter = $this->filters($request);
        $requestFilter = $request->all();
        $applications = null;
        if( isset($requestFilter['search']) ) {
            $paginate = $filter['paginate'] ?? PdoiApplication::PAGINATE;
            $appQ = PdoiApplication::with(['responseSubject', 'responseSubject.translations'])
                ->FilterBy($request->all())
                ->SortedBy($request->input('sort'),$request->input('ord'));

            $applications = (new PdoiApplicationShortCollection($appQ->paginate($paginate)))->resolve();
        }
        $titlePage =__('custom.searching');
        return view('front.application.list', compact('applications', 'titlePage', 'filter'));
    }

    public function show(Request $request, int $id = 0)
    {
        $item = $this->getFullRecord($id);
        if( !$item ) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $application = (new PdoiApplicationShortResource($item))->resolve();
        return view('front.application.view', compact('application'));
    }

    public function myApplications(Request $request)
    {
        $paginate = $request->filled('paginate') ? $request->get('paginate') : PdoiApplication::PAGINATE;
        $appQ = PdoiApplication::with(['responseSubject', 'responseSubject.translations'])
            ->FilterBy($request->all())
            ->SortedBy($request->input('sort'),$request->input('ord'))
            ->where('user_reg', $request->user()
                ->id);
        $applications = (new PdoiApplicationShortCollection($appQ->paginate($paginate)))->resolve();
        $myList = true;
        $titlePage =__('front.my_application.title');
        return view('front.application.list', compact('applications', 'titlePage', 'myList'));
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
        return view('front.my_application.view', compact('application'));
    }



    public function create(Request $request)
    {
        $user = User::with(['country', 'area', 'municipality', 'settlement'])->find((int)$request->user()->id);

        if( !$user || $user->cannot('create', PdoiApplication::class) ) {
            return back()->with('warning', __('messages.unauthorized'));
        }
        $title = __('front.application.title.apply');

        $data=[];
        if( !$user->country ) {
            $data['countries'] = Country::optionsList();
        }
        if( !$user->area ) {
            $data['areas'] = EkatteArea::optionsList();
        }
        if( !$user->municipality ) {
            $data['municipality'] = EkatteMunicipality::optionsList();
        }
        if( !$user->settlement ) {
            $data['settlements'] = EkatteSettlement::optionsList();
        }

        $rzs = PdoiResponseSubject::optionsList();
        return view('front.application.apply', compact('data', 'user', 'rzs', 'title'));
    }

    public function store(Request $request) {

        $user = $request->user();
        if( !$user || $user->cannot('create', PdoiApplication::class) ) {
            return response()->json(['warning' => __('messages.unauthorized')], 200);
        }

        $applicationRequest = new PdoiApplicationApplyRequest();
        $validator = Validator::make($request->all(), $applicationRequest->rules());

        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 200);
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

                $appService = new ApplicationService($newApplication);
                //Register event: register first app event
                $receivedEvent = $appService->registerEvent(ApplicationEventsEnum::SEND->value);

                if ( is_null($receivedEvent) ) {
                    DB::rollBack();
                    logError('Apply application (front): ', 'Operation roll back because cant\'t register '.ApplicationEventsEnum::SEND->name. ' event');
                    return response()->json(['errors' => __('custom.system_error')], 200);
                }

                //Save user attached files
                if( isset($validated['files']) && sizeof($validated['files']) ) {
                    foreach ($validated['files'] as $key => $file) {
                        $fileNameToStore = ($key + 1).'_'.round(microtime(true)).'.'.$file->getClientOriginalExtension();
                        // Upload File
                        $file->storeAs($newApplication->fileFolder, $fileNameToStore, 'local');
                        $newFile = new File([
                            'code_object' => File::CODE_OBJ_APPLICATION,
                            'filename' => $fileNameToStore,
                            'content_type' => $file->getClientMimeType(),
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
                $subject->notify(new NotifySubjectNewApplication($newApplication));

                //TODO fix me simulation remove after communication is ready. For now we simulate approve by RKS (деловодна система)
                $lastNotify = DB::table('notifications')
                    ->where('type', 'App\Notifications\NotifySubjectNewApplication')
                    ->latest()->limit(1)->get()->pluck('id');
                if(isset($lastNotify[0])) {
                    $appService = new ApplicationService($newApplication);
                    $appService->communicationCallback(json_encode(['notification_id' => $lastNotify[0]]));
                }


                $fileName = 'zayavlenie_ZDOI_'.displayDate($newApplication->created_at).'.pdf';
                $pdfFile = Pdf::loadView('pdf.application_doc', ['application' => $newApplication]);
                Storage::disk('local')->put($newApplication->fileFolder.$fileName, $pdfFile->output());
                $newFile = new File([
                    'code_object' => File::CODE_OBJ_APPLICATION,
                    'filename' => $fileName,
                    'content_type' => 'application/pdf',
                    'path' => $newApplication->fileFolder.$fileName,
                ]);
                $newApplication->files()->save($newFile);

                $newApplication->refresh();
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
                'options' => optionsFromModel(PdoiApplication::optionsList(), true,''),
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
            'settlement', 'settlement.translations', 'events', 'events.event'])
            ->find((int)$id);
    }
}
