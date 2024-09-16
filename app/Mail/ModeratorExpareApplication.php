<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ModeratorExpareApplication extends Mailable
{
    use Queueable, SerializesModels;

    private $app;
    private $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($app, $user)
    {
        $this->app = $app;
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
            ->subject('ПДОИ: Изтичащ срок за обработка ['.$this->app->application_uri.']')
            ->markdown('emails.moderator_soon_expire_app', ['url' => route('admin.application.view', $this->app), 'app' => $this->app, 'user' => $this->user]);
    }
}
