<?php

namespace App\Console\Commands;

use App\Models\File;
use App\Models\PdoiApplication;
use App\Services\ApplicationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
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
        $beforeTimestamp = Carbon::now()->subHours(1);
        $notifications = DB::table('notifications')
            ->where('type_channel','=', self::EMAIL_CHANNEL)
            ->where('cnt_send','<=', self::MAX_TRY)
            ->where('is_send','=', 0)
            ->where('updated_at','<=', $beforeTimestamp)
            ->get();

        if( $notifications->count() ) {
            foreach ($notifications as $item) {
                $messageData = json_decode($item->message, true);
                if( !$messageData ) {
                    logError('Send email notification ID '.$item->id, 'Invalid json message data');
                    continue;
                }

                DB::beginTransaction();
                try {
                    Mail::send([], [], function ($message) use ($messageData){
                        $message->from($messageData['from_mail'])
                            ->to(env('APP_ENV') == 'local' ? env('LOCAL_TO_MAIL') : $messageData['to_email'])
                            ->subject($messageData['subject'])
                            ->setBody($messageData['message'],'text/html');

                        if( isset($messageData['files']) && sizeof($messageData['files']) ) {
                            $files = File::whereIn('id', $messageData['files'])->get();
                            if( $files->count() ){
                                foreach ($files as $f) {
                                    $message->attach(Storage::disk('local')->get($f->path));
                                }
                            }
                        }
                    });
                    $application = PdoiApplication::find((int)$messageData['application_id']);
                    $appService = new ApplicationService($application);
                    $appService->communicationCallback($item);

                    DB::commit();
                } catch (\Exception $e) {
                    logError('Send email notification ID '.$item->id, $e->getMessage());
                    DB::rollBack();
                }

            }
        }
    }
}
