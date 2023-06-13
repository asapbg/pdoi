<?php

namespace App\Mail;

use App\Models\PdoiApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotiyUserApplicationStatus extends Mailable
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
        $view = view()->exists('emails.'.app()->getLocale().'.new_application_status' ) ?
            'emails.'.app()->getLocale().'.new_application_status'
            : 'emails.'.config('app.locale').'.new_application_status';

        return $this->from($from)
            ->subject(__('mail.subject.new_application_status'))
            ->markdown($view, ['application' => $this->application]);
    }
}
