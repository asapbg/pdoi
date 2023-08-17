<?php

namespace App\Console\Commands;

use App\Models\File;
use App\Models\PdoiApplication;
use App\Services\ApplicationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendEmailNotifications extends Command
{
    const MAX_TRY = 3;
    const EMAIL_CHANNEL= 1;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send all notification by emails';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info("Cron run notification:email.");

        $beforeTimestamp = Carbon::now()->subHours(1);
        $notifications = DB::table('notifications')
            ->where('type_channel','=', self::EMAIL_CHANNEL)
            ->where('cnt_send','<', self::MAX_TRY)
            ->where('is_send','=', 0)
            ->where(function ($q) use ($beforeTimestamp){
                $q->where('updated_at','<=', $beforeTimestamp)
                    ->orWhere('created_at', '>=', $beforeTimestamp);
            })
            ->get();
        if( $notifications->count() ) {
            foreach ($notifications as $item) {
                $messageData = json_decode($item->data, true);
                if( !$messageData ) {
                    logError('Send email notification ID '.$item->id, 'Invalid json message data');
                    continue;
                }

                DB::beginTransaction();
                try {
                    Mail::send([], [], function ($message) use ($messageData){
                        $message->from($messageData['from_email'])
                            ->to(env('APP_ENV') != 'production' ? env('LOCAL_TO_MAIL') : $messageData['to_email'])
                            ->subject($messageData['subject'])
                            ->html($messageData['message'])
                            ->text($messageData['message']);

                        if( isset($messageData['files']) && sizeof($messageData['files']) ) {
                            $files = File::whereIn('id', $messageData['files'])->get();
                            if( $files->count() ){
                                foreach ($files as $f) {
                                    $message->attach(base_path().Storage::disk('local')->url('app'.DIRECTORY_SEPARATOR.$f->path));
                                }
                            }
                        }
                    });
                    $application = PdoiApplication::find((int)$messageData['application_id']);
                    $appService = new ApplicationService($application);
                    $appService->communicationCallback($item);

                    DB::table('notifications')
                        ->where('id', $item->id)
                        ->update(['is_send' => 1, 'cnt_send' => ($item->cnt_send + 1)]);
                    DB::commit();
                } catch (\Exception $e) {
                    logError('Send email notification ID '.$item->id, $e->getMessage());
                    DB::rollBack();
                    DB::table('notifications')
                        ->where('id', $item->id)
                        ->update(['cnt_send' => ($item->cnt_send + 1)]);
                }

            }
        }
    }
}
