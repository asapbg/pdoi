<?php

namespace App\Mail;

use App\Models\PdoiApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubjectRegisterNewApplication extends Mailable
{
    use Queueable, SerializesModels;

    private PdoiApplication $application;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(PdoiApplication $application)
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
        $view = view()->exists('emails.'.app()->getLocale().'.register_new_application' ) ?
            'emails.'.app()->getLocale().'.register_new_application'
            : 'emails.'.config('app.locale').'.register_new_application';

        return $this->from($from)
            ->subject(__('mail.subject.register_new_application'))
            ->markdown($view, ['application' => $this->application]);
    }
}
