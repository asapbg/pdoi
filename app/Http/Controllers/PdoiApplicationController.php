<?php

namespace App\Http\Controllers;

use App\Enums\PdoiApplicationStatusesEnum;
use App\Enums\PdoiSubjectDeliveryMethodsEnum;
use App\Http\Requests\PdoiApplicationApplyRequest;
use App\Mail\NotiyUserApplicationStatus;
use App\Mail\SubjectRegisterNewApplication;
use App\Models\Country;
use App\Models\EkatteArea;
use App\Models\EkatteMunicipality;
use App\Models\EkatteSettlement;
use App\Models\File;
use App\Models\PdoiApplication;
use App\Models\PdoiResponseSubject;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PdoiApplicationController extends Controller
{
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
                //TODO generate application for each Subject
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
                    'request' => htmlentities($validated['request']),
                    'status' => PdoiApplicationStatusesEnum::RECEIVED->value,
                    'application_uri' => round(microtime(true)).'-'. displayDate(Carbon::now()),
                    'email_publication' => $validated['email_publication'] ?? 0,
                    'names_publication' => $validated['names_publication'] ?? 0,
                    'address_publication' => $validated['address_publication'] ?? 0,
                    'user_attached_files' => isset($validated['files']) ? sizeof($validated['files']) : 0,
                    'phone_publication' => $validated['phone_publication'] ?? 0
                    //TODO ask what is this column
//                    'headoffice_publication' => $validated['headoffice_publication'] ?? 0,
                ]);
                $newApplication->save();

                //TODO save user attached files

                //REGISTER APPLICATION DEPENDING ON SUBJECT DELIVERY METHOD
                //now SUBJECTS has 3 methods for delivery (mail, SDES, RKS)
                $subject = $newApplication->responseSubject;
                switch ($subject->delivery_method) {
//                    case PdoiSubjectDeliveryMethodsEnum::SDES: //ССЕВ
//                        //TODO create service for SDES
//                        break;
//                    case PdoiSubjectDeliveryMethodsEnum::RKS: //Деловодна система
//                        //TODO create service for RKS
//                        // автоматично препращане към съответната деловодна система на задължения субект, което се
//                        // извършва чрез Системата за електронен обмен на съобщения (СЕОС). Статусът на заявлението се
//                        // променя на „Очаква регистрация при задължен субект“. На заявителя се изпраща съобщение,
//                        // че заявлението очаква регистрация при задължения субект.
//                        $newApplication->status = PdoiApplicationStatusesEnum::REGISTRATION_TO_SUBJECT->value;
//                        $newApplication->status_date = Carbon::now();
//                        // След получаване на потвърждение от деловодната система на задължения субект, статусът на заявлението става
//                        // „Регистрирано/в процес на обработка“ и автоматично стартира 14-дневен срок за решение.
//                        // Платформата изпраща автоматично e-mail съобщение до заявителя, с което го уведомява за стартиралата
//                        // обработка на заявлението и за срока за решение.
//                        $newApplication->status = PdoiApplicationStatusesEnum::IN_PROCESS->value;
//                        $newApplication->status_date = Carbon::now();
//                        $newApplication->registration_date = Carbon::now();
//                        $newApplication->response_end_time = Carbon::now()->addDays(14);//14 дни от регситрацията на зявлението при ЗС
//                        // В случай, че деловодната система на задължения субект не е интегрирана със Системата за електронен
//                        // обмен на съобщения (СЕОС), 14-дневният срок за решение стартира при регистриране на заявлението в платформата,
//                        // за което администраторът на съответния профил се уведомява с e-mail.
//                        //TODO send mail to subject
//                        $newApplication->status = PdoiApplicationStatusesEnum::IN_PROCESS->value;
//                        $newApplication->status_date = Carbon::now();
//                        $newApplication->registration_date = Carbon::now();
//                        $newApplication->response_end_time = Carbon::now()->addDays(14);//14 дни от регситрацията на зявлението при ЗС
//                        break;
                    default:
                        //delivery_method is email or not set
                        $newApplication->status = PdoiApplicationStatusesEnum::IN_PROCESS->value;
                        $newApplication->status_date = Carbon::now();
                        $newApplication->registration_date = Carbon::now();
                        $newApplication->response_end_time = Carbon::now()->addDays(PdoiApplication::DAYS_AFTER_SUBJECT_REGISTRATION);
                        $newApplication->save();

                        //send mail to subject
                        Mail::to($user->email)->send(new SubjectRegisterNewApplication($newApplication));
//                        Mail::to($subject->email)->send(new SubjectRegisterNewApplication($newApplication));
                }

                //return info for each generated application
                $data['applicationsInfo'][] = array(
                    'reg_number' => $newApplication->application_uri,
                    'response_subject' => $newApplication->responseSubject->subject_name,
                    'status' => $newApplication->statusName,
                    'status_date' => displayDate($newApplication->status_date),
                    'response_end_time' => displayDate($newApplication->response_end_time),
                );
                //TODO send mail for application status to user
                Mail::to($user->email)->send(new NotiyUserApplicationStatus($newApplication));
//                Mail::to($newApplication->applicant->email)->send(new NotiyUserApplicationStatus($newApplication));

                //TODO Generate application PDF file
                // When generate this file only on registration or???
                $fileName = 'zayavlenie_ZDOI_'.displayDate($newApplication->created_at).'.pdf';
                $pdfFile = Pdf::loadView('pdf.application_doc', ['application' => $newApplication]);
                Storage::disk('local')->put($newApplication->fileFolder.$fileName, $pdfFile->output());
                $newFile = new File([
                    'code_object' => PdoiApplication::CODE_OBJECT,
                    'filename' => $fileName,
                    'content_type' => 'application/pdf',
                    'path' => $newApplication->fileFolder.$fileName,
                ]);
                $newApplication->files()->save($newFile);
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
}
