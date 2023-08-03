<?php

namespace App\Notifications;

use App\Enums\PdoiSubjectDeliveryMethodsEnum;
use App\Models\Egov\EgovMessage;
use App\Models\Egov\EgovOrganisation;
use App\Models\PdoiApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Selective\XmlDSig\PrivateKeyStore;
use Selective\XmlDSig\Algorithm;
use Selective\XmlDSig\CryptoSigner;
use Selective\XmlDSig\XmlSigner;

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
            'files' => $this->application->files ? $this->application->files->pluck('id')->toArray() : [],
            'type_channel' => $notifiable->delivery_method
        ];
        switch ($notifiable->delivery_method)
        {
            case PdoiSubjectDeliveryMethodsEnum::SDES->value: //система за сигурно електронно връчване
                break;
            case PdoiSubjectDeliveryMethodsEnum::SEOS->value: //деловодна система
                $sender = $this->application->parent_id ? $this->application->parent->responseSubject->egovOrganisation : EgovOrganisation::where('eik', env('SEOS_PLATFORM_EIK',null))->first();
                $receiver = env('APP_ENV') != 'production' ? EgovOrganisation::find((int)env('LOCAL_TO_EGOV_ORGANISATION_ID')) : $this->application->responseSubject->egovOrganisation;

                $sender = $receiver;
                $service = $receiver?->services()->first();
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
                        //dd($this->generateSeosXml($sender, $receiver, $messageContent, $this->application, $egovMessage));
                        $egovMessage->msg_xml = $this->generateSeosXml($sender, $receiver, $messageContent, $this->application, $egovMessage);
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
        }
        return $communicationData;
    }

    private function generateSeosXml($sender, $receiver, $messageContent, $application, $egovMessage): string
    {
        $xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:mes="http://services.egov.bg/messaging">
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
            </soapenv:Header>
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
                $xml .= '<DocAttachmentList>
                        <Attachment>
                            <AttFileName>'.$f->filename.'</AttFileName>
                            <AttBody>'.base64_encode(Storage::disk('local')->get($f->path)).'</AttBody>
                            <AttComment>'.$f->description.'</AttComment>
                            <AttMIMEType>'.$f->content_type.'</AttMIMEType>
                        </Attachment>
                    </DocAttachmentList>
                ';
            }
        }
                $xml .='</DocumentType>
                <Comment>'.$messageContent.'</Comment>
              </DocumentRegistrationRequestType>
            </soapenv:Body>
            </soapenv:Envelope>
        ';

        $privateKeyStore = new PrivateKeyStore();
        // load a private key from a string
        $privateKeyStore->loadFromPem(file_get_contents(env('SEOS_SERVER_CERT_KEY_PATH')), file_get_contents(env('SEOS_SERVER_CERT_PATH')));
        //Define the digest method: sha1, sha224, sha256, sha384, sha512
        $algorithm = new Algorithm(Algorithm::METHOD_SHA1);
        //Create a CryptoSigner instance:
        $cryptoSigner = new CryptoSigner($privateKeyStore, $algorithm);
        // Create a XmlSigner and pass the crypto signer
        $xmlSigner = new XmlSigner($cryptoSigner);
        // Create a signed XML string
        return $xmlSigner->signXml($xml);
    }
}
