<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserRejectRenewRequest extends Mailable
{
    use Queueable, SerializesModels;

    private $app_request;
    private $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($apprequest, $user)
    {
        $this->app_request = $apprequest;
        $this->user = $user;
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
            ->subject('ПДОИ: Отказ за възобновяване на завление ['.$this->app_request->application->application_uri.']')
            ->markdown('emails.reject_renew_application', ['url' => route('application.my.show', $this->app_request->application->id), 'app_request' => $this->app_request, 'user' => $this->user]);
    }
}
