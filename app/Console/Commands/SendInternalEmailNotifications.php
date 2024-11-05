<?php

namespace App\Console\Commands;

use App\Models\File;
use App\Models\PdoiApplication;
use App\Models\ScheduledMessage;
use App\Models\User;
use App\Notifications\CustomInternalNotification;
use App\Services\ApplicationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendInternalEmailNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send all notification from schedule';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info("Cron run notification:schedule");

        $notification = ScheduledMessage::where('is_send', '=', 0)
            ->where('start_at', '<=', databaseDateTime(Carbon::now()))
            ->orderBy('id', 'desc')
            ->first();
//dd($notification->id);
        if( $notification ) {
            $messageData = json_decode($notification->data, true);
            if( !$messageData ) {
                Log::error('Send scheduled notification ID '.$notification->id. ': '.' Invalid json message data');
                exit;
            }

            $usersIds = json_decode($notification->send_to);
            if( !$usersIds ) {
                Log::error('Send scheduled notification ID '.$notification->id. ': '.' Missing users ids');
                exit;
            }

            $users = User::whereIn('id', $usersIds)->get();
            if( !$users ) {
                Log::error('Send scheduled notification ID '.$notification->id. ': '.' Missing users');
                exit;
            }

//            $allUsers = $users->count();
            $notReceivedByEmail = [];
            $receivedByEmail = [];
            $notReceivedByApp = [];
            $receivedByApp = [];
            foreach ($users as $user){
                if($notification->by_app){
                    try {
                        $user->notify(new CustomInternalNotification($messageData));
                        $receivedByApp[] = $user->id;
                    } catch (\Exception $e){
                        Log::error($e);
                        $notReceivedByApp[] = $user->id;
                    }
                }
                if($notification->by_email){
                    $to = config('app.env') != 'production' ? config('mail.local_to_mail') : $user->email;

                    try {
                        $myMessage = str_replace('\r\n', '', strip_tags(html_entity_decode($messageData['msg'])));
                        $myMessage = clearText($myMessage);
                        Mail::send([], [], function ($message) use ($messageData, $to, $myMessage){
                            $message->from(config('mail.from.address'))
                                ->to($to)
                                ->subject($messageData['subject'])
                                ->html($messageData['msg'])
                                ->text($myMessage);
                        });
                        $receivedByEmail[] = $user->id;
                        sleep(1);
                    } catch (\Exception $e){
                        Log::error($e);
                        $notReceivedByEmail[] = $user->id;
                    }
                }
            }

            if(sizeof($receivedByApp) || sizeof($receivedByEmail)){
                $notification->is_send = 1;
                $notification->send_at = databaseDateTime(Carbon::now());
                $notification->not_send_to_by_email = sizeof($notReceivedByEmail) ? json_encode($notReceivedByEmail) : null;
                $notification->not_send_to_by_app = sizeof($notReceivedByApp) ? json_encode($notReceivedByApp) : null;
                $notification->save();
            }
        }
    }
}
