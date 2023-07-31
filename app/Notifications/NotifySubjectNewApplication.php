<?php

namespace App\Notifications;

use App\Enums\ApplicationEventsEnum;
use App\Enums\PdoiSubjectDeliveryMethodsEnum;
use App\Models\Egov\EgovMessage;
use App\Models\Egov\EgovOrganisation;
use App\Models\PdoiApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
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
    public function __construct($application, $data, $egovMessageId = null)
    {
        $this->application = $application;
        $this->notifyData = $data;
        $this->egov_message_id = $egovMessageId;
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
//            'message' => $message,
//            'subject' => __('mail.subject.register_new_application'),
            'application_id' => $this->application->id,
//            'files' => [],
            'type_channel' => $notifiable->delivery_method
        ];
        switch ($notifiable->delivery_method)
        {
            case PdoiSubjectDeliveryMethodsEnum::SDES->value: //система за сигурно електронно връчване
                break;
            case PdoiSubjectDeliveryMethodsEnum::RKS->value: //деловодна система
                $sender = $this->application->parent_id ? $this->application->parent->responseSubject->egovOrganisation : EgovOrganisation::where('eik', env('SEOS_PLATFORM_EIK',null))->first();
                $receiver = $this->application->responseSubject->egovOrganisation;
                $service = $receiver?->services->latest();
                if( $sender && $receiver && $service) {
                    $egovMessage = new EgovMessage([
                        'msg_guid' => Str::uuid(),
                        'msg_type' => EgovMessage::TYPE_REGISTER_DOCUMENT,
                        'sender_guid' => $sender->guid,
                        'sender_name' => $sender->administrative_body_name,
                        'sender_eik' => $sender->eik,
                        'recipient_guid' => $receiver->guid,
                        'recipient_name' => $receiver->administrative_body_name,
                        'recipient_eik' => $receiver->eik,
                        'msg_version' => $service->version
                    ]);
                    $egovMessage->save();
                    if( $egovMessage->id ) {
                        $egovMessage->msg_xml = $this->generateSeosXml($sender, $receiver, $messageContent, $this->application, $egovMessage);
                        $egovMessage->save();
                    }
                }
                $communicationData['egov_messag_id'] = $egovMessage && $egovMessage->id ? $egovMessage->id : null;

                break;
            default://email
                $communicationData['from_name'] = config('mail.from.name');
                $communicationData['from_email'] = config('mail.from.address');
                $communicationData['to_name'] = $notifiable->names;
                $communicationData['to_email'] = $notifiable->email;
        }
        return $communicationData;
    }

    private function generateSeosXml($sender, $receiver, $messageContent, $application, $egovMessage): string
    {
        $xml = '
            <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:mes="http://services.egov.bg/messaging">
            <soapenv:Header>
                <Version>'.$egovMessage->msg_version.'</Version>
                <MessageType>'.$egovMessage->msg_type.'</MessageType>
                <MessageDate>'.$egovMessage->created_at.'</MessageDate>
                <Sender>
                    <Identifier>'.$sender->eik.'</Identifier>
                    <AdministrativeBodyName>'.$sender->administrative_body_name.'</AdministrativeBodyName>
                    <GUID>'.$sender->guid.'</GUID>
                </Sender>
                <Recipient>
                    <Identifier>'.$receiver->eik.'</Identifier>
                    <AdministrativeBodyName>'.$receiver->administrative_body_name.'</AdministrativeBodyName>
                    <GUID>'.$receiver->guid.'</GUID>
                </Recipient>
                <MessageGUID>'.$egovMessage->msg_guid.'</MessageGUID>
            <soapenv:Header/>
            <soapenv:Body>
              <DocumentRegistrationRequestType>
                  <DocumentType>
                    <DocID>
                        <DocumentGUID>'.$application->doc_guid.'</DocumentGUID>
                    </DocID>
                    <DocKind>Заявление за достъп до обществена информация</DocKind>';
        //add files
        if($application->files) {
            foreach ($application->files as $f) {
                $xml .= '
                    <DocAttachmentList>
                        <Attachment>
                            <AttFileName>'.$f->filename.'</AttFileName>
                            <AttBody>'.Storage::disk('local')->get($f->path).'</AttBody>
                            <AttComment>'.$f->description.'</AttComment>
                            <AttMIMEType>'.$f->content_type.'</AttMIMEType>
                        </Attachment>
                    </DocAttachmentList>
                ';
            }
        }
                $xml .='
                </DocumentType>
                <Comment>'.$messageContent.'</Comment>
              </DocumentRegistrationRequestType>
            </soapenv:Body>
            <ds:Signature>{Траснпортен сертификат ?}</ds:Signature>
            </soapenv:Envelope>
        ';
        return $xml;
    }
}
