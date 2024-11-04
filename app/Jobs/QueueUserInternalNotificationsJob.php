<?php

namespace App\Jobs;

use App\Notifications\CustomInternalNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class QueueUserInternalNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    public $users;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($users, $data)
    {
        $this->data = $data;
        $this->users = $users;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->users as $user){
            if($this->data['internalMsg']){
                unset($this->data['internalMsg']);
                $user->notify(new CustomInternalNotification($this->data, 'internalMsg'));
            }
            if($this->data['mailMsg']){
                unset($this->data['mailMsg']);
                $user->notify(new CustomInternalNotification($this->data, 'mailMsg'));
            }
        }
    }
}
