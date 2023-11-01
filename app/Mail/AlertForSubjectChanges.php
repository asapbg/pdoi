<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AlertForSubjectChanges extends Mailable
{
    use Queueable, SerializesModels;

    private $mailData;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
//        'subject' => pdoi response subject,
//        'new_level' =>  RzsSection model item,
//        'new_parent' => PdoiResponseSubject model item
//        'new_status' => pdoi response subject active/inactive boolean
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $from = config('mail.from.address');
        $msg = '';
        if( isset($this->mailData['new_status']) ) {
            $msg .= ('Промяна в статуса: '.($this->mailData['new_status'] ? 'Активен' : 'Неактивен'));
        }
        if( isset($this->mailData['new_level']) ) {
            $msg .= ('Преместен в Административния сектор: '.$this->mailData['new_level']->name);
        }
        if( isset($this->mailData['new_parent']) ) {
            $msg .= ('Преместен в структурата: '.$this->mailData['new_parent']->subject_name);
        }
        return $this->from($from)
            ->subject('Промяна в статус/структура на задължен субект')
            ->markdown('emails.subject_changes', ['msg' => $msg, 'subject_name' => $this->mailData['subject']->subject_name]);
    }
}
