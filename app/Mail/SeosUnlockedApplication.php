<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SeosUnlockedApplication extends Mailable
{
    use Queueable, SerializesModels;

    private $application;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($application)
    {
        $this->application = $application;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $from = config('mail.from.address');
        return $this->from($from)
            ->subject('ПДОИ: Заявление за обработка')
            ->markdown('emails.seos_unlocked_application', ['url' => route('admin.application.view', ['item' => $this->application->id]), 'reg_num' => $this->application->application_uri, 'subject_name' => $this->application->responseSubject->subject_name ]);
    }
}
