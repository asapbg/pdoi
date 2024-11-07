<?php

namespace App\Notifications;

use App\Enums\PdoiSubjectDeliveryMethodsEnum;
use App\Models\Egov\EgovMessage;
use App\Models\Egov\EgovOrganisation;
use App\Models\PdoiApplication;
use Carbon\Carbon;
use DOMDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NotifySubjectNewApplication extends Notification
{
    use Queueable;

    private PdoiApplication $application;
    private array $notifyData;
    private int|null $egov_message_id;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($application, $data)
    {
        $this->application = $application;
        $this->notifyData = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [CustomDbChannel::class];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable): array
    {
        $messageContent = $this->notifyData['message'];
        if( isset($this->notifyData['comment']) && !empty($this->notifyData['comment']) ) {
            $messageContent .= PHP_EOL.'-------------------'.PHP_EOL.'Допълнителен коментар:'. $this->notifyData['comment'];
        }
        $communicationData = [
            'message' => $messageContent,
            'subject' => __('mail.subject.register_new_application'),
            'application_id' => $this->application->id,
            'files' => [],
            'type_channel' => $notifiable->delivery_method
        ];
        switch ($notifiable->delivery_method)
        {
            case PdoiSubjectDeliveryMethodsEnum::SDES->value: //система за сигурно електронно връчване
                $eDeliveryConfig = config('e_delivery');
                if( config('app.env') != 'production' ) {
                    $communicationData['ssev_profile_id'] = config('e_delivery.local_ssev_profile_id');
                } else {
                    $communicationData['to_group'] = $notifiable->eik == '175370880' ? $eDeliveryConfig['group_ids']['company'] : $eDeliveryConfig['group_ids']['egov'];
                    $communicationData['to_identity'] = $notifiable->eik;
                    $communicationData['ssev_profile_id'] = $notifiable->ssev_profile_id ?? 0;
                }

                if( $this->application->files->count() ) {
                    foreach ($this->application->files as $f) {
                        $communicationData['files'][] = [
                            'id' => $f->id,
                            'name' => $f->filename,
                            'content_type' => $f->content_type,
                            'binary_content' => base64_encode(Storage::disk('local')->get($f->path))
                        ];
                    }
                }
                break;
            case PdoiSubjectDeliveryMethodsEnum::SEOS->value: //деловодна система
                $communicationData['files']= $this->application->files ? $this->application->files->pluck('id')->toArray() : [];
                //$sender = $this->application->parent_id ? $this->application->parent->responseSubject->egovOrganisation : EgovOrganisation::where('eik', config('seos.eik'))->first();
                $sender = EgovOrganisation::where('eik', config('seos.eik'))->first();
                $envProd = config('app.env') == 'production';
                $senderInfo = [
                    'guid' => $envProd ? $sender->guid : '{B2A43E54-B6CB-4835-A6CC-D05692B3F7CD}',
                    'eik' => $envProd ? $sender->eik : '0006950252000',
                    'name' => $envProd ? $sender->administrative_body_name : 'Платформа за достъп до обществена информация'
                ];
                $receiver = config('app.env') != 'production' ? EgovOrganisation::find((int)config('seos.local_egov_org_id')) : $this->application->responseSubject->egovOrganisation;

                //$sender = $receiver;
                $service = $receiver?->services()->first();
                if( $senderInfo && $receiver && $service) {
                    $egovMessage = new EgovMessage([
                        'msg_guid' => Str::uuid(),
                        'doc_guid' => Str::uuid(),
                        'msg_type' => EgovMessage::TYPE_REGISTER_DOCUMENT,
                        'sender_guid' => $senderInfo['guid'],
                        'sender_name' => $senderInfo['name'],
                        'sender_eik' => $senderInfo['eik'],
                        'recipient_guid' => $receiver->guid,
                        'recipient_name' => $receiver->administrative_body_name,
                        'recipient_eik' => $receiver->eik,
                        'msg_version' => $service->version,
                        'msg_comment' => 'N/A',//$messageContent,
                        'doc_vid' => 'Заявление за достъп до обществена информация',
                        'doc_rn' => $this->application->application_uri,
                        'doc_dat' => Carbon::parse($this->application->created_at, 'UTC')->format('Y-m-d')
                    ]);
                    $egovMessage->save();
                    if( $egovMessage->id ) {
                        $egovMessage->msg_xml = $this->generateSeosXml($senderInfo, $receiver, $messageContent, $this->application, $egovMessage);
                        $egovMessage->save();
                    }
                }
                $communicationData['egov_messag_id'] = isset($egovMessage) && $egovMessage && $egovMessage->id ? $egovMessage->id : null;

                break;
            default://email
                $communicationData['from_name'] = config('mail.from.name');
                $communicationData['from_email'] = config('mail.from.address');
                $communicationData['to_name'] = $notifiable->names;
                $communicationData['to_email'] = $notifiable->email;
//                $communicationData['files']= $this->application->files ? $this->application->files->pluck('id')->toArray() : [];
                $communicationData['files']= [];
        }
        return $communicationData;
    }

    private function generateSeosXml($sender, $receiver, $messageContent, $application, $egovMessage): string
    {
        //add files
        $docList = '';
        if($application->files) {
            $docList .= '<DocAttachmentList>';
            foreach ($application->files as $f) {
                $docList .= '<Attachment><AttFileName>'.$f->filename.'</AttFileName><AttBody>'.base64_encode(Storage::disk('local')->get($f->path)).'</AttBody><AttMIMEType>text/unknown</AttMIMEType></Attachment>';
            }
            $docList .= '</DocAttachmentList>';
        }

        //MessageDate = 2023-03-29T17:14:48.177+03:00
        $xml = '<?xml version="1.0" encoding="UTF-8"?><Message xmlns="http://schemas.egov.bg/messaging/v1" xmlns:ns2="http://ereg.egov.bg/segment/0009-000001" xmlns:ns3="http://www.w3.org/2000/09/xmldsig#"><Header><Version>'.$egovMessage->msg_version.'</Version><MessageType>'.$egovMessage->msg_type.'</MessageType><MessageDate>'.(Carbon::now()->format('Y-m-d\TH:i:s.vP')).'</MessageDate><Sender><Identifier>'.$sender['eik'].'</Identifier><AdministrativeBodyName>'.$sender['name'].'</AdministrativeBodyName><GUID>'.$sender['guid'].'</GUID></Sender><Recipient><Identifier>'.$receiver->eik.'</Identifier><AdministrativeBodyName>'.$receiver->administrative_body_name.'</AdministrativeBodyName><GUID>'.$receiver->guid.'</GUID></Recipient><MessageGUID>{'.$egovMessage->msg_guid.'}</MessageGUID></Header><Body><DocumentRegistrationRequest><Document><DocID><DocumentNumber><DocNumber>'.$application->application_uri.'</DocNumber><DocDate>'.$egovMessage->doc_dat.'</DocDate></DocumentNumber><DocumentGUID>{'.$egovMessage->doc_guid.'}</DocumentGUID></DocID><DocKind>'.$egovMessage->doc_vid.'</DocKind>'.$docList.'<DocAbout>'.$application->application_uri.' '.$egovMessage->doc_vid.'</DocAbout><DocComment>'.$application->application_uri.' '.$egovMessage->doc_vid.'</DocComment></Document><Comment>N/A</Comment></DocumentRegistrationRequest></Body></Message>';
        return '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/"><s:Body><Submit xmlns="http://services.egov.bg/messaging/"><request>'.($this->sign($xml)).'</request></Submit></s:Body></s:Envelope>';
    }

    /**
     * Sign xml
     * @param $xmlString
     */
    public static function sign($xmlString)
    {
        file_put_contents(config('filesystems.scripts_directory').'seos_test.xml', $xmlString);
        shell_exec('php '.config('seos.sign_script'));
        sleep(1);
        $signedXml = file_get_contents(config('filesystems.scripts_directory').'seos_signTest.xml');
        return str_replace(['<','>',"\n", "\r"], ['&lt;','&gt;',"", ""], $signedXml);
    }
}
