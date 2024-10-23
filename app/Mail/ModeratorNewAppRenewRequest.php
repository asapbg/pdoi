<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ModeratorNewAppRenewRequest extends Mailable
{
    use Queueable, SerializesModels;

    private $url;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($url)
    {
        $this->url = $url;
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
            ->subject('ПДОИ: Заявка за възобновяване')
            ->markdown('emails.moderator_new_app_request', ['url' => $this->url]);
    }
}
