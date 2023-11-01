<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UsersChangePassword extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     *
     * @param $user
     */
    public function __construct($user)
    {
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
        $baseUrl = config('app.url');
        if(substr($baseUrl, -1) != "/") $baseUrl .= "/";

        return $this->from($from)
                    ->subject('Създаване на потребителски профил')
                    ->markdown('emails.change-password', ['url' => $baseUrl."auth/password/user-change/".$this->user->id]);
    }


}
