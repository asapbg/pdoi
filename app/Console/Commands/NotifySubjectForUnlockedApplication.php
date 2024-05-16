<?php

namespace App\Console\Commands;

use App\Enums\PdoiApplicationStatusesEnum;
use App\Mail\SeosUnlockedApplication;
use App\Models\PdoiApplication;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifySubjectForUnlockedApplication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:unlocked_applications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'After too many unsuccessful try to send application to SESO we block this operation and unlock application for next events. This job notify moderators and subject that they have an application in process';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $apps = PdoiApplication::where('seos_error_alert', '=', 1)->where('status', '=', PdoiApplicationStatusesEnum::IN_PROCESS->value)
            ->get();
//        dd($apps);
        if($apps->count()){
            foreach ($apps as $app){
                try {
                    $foundMails = false;
//                    $foundMails = false;
//                    $pdoiSubject = $app->responseSubject;
//                    if($pdoiSubject){
//                        if(!empty($pdoiSubject->email)){
//                            $foundMails = true;
//                            //subject notification
//                            Mail::to($pdoiSubject->email)->send(new SeosUnlockedApplication($app));
//                            sleep(2);
//                        }
//
//                        $emailList = $pdoiSubject->getConnectedUsersEmails();
//                        if( sizeof($emailList) ) {
//                            $foundMails = true;
//                            foreach ($emailList as $mail){
//                                Mail::to($mail)->send(new SeosUnlockedApplication($app));
//                                sleep(2);
//                            }
//                        }
//                    }
                    $foundMails = true;// Skip sending mails for now
                    if($foundMails){
                        $app->seos_error_alert = 0;
                        $app->save();
                    }
                } catch (\Exception $e){
                    Log::error('Notify for unlocked application (ID - '.$app->id.'): '.$e);
                }

            }
        }

        return Command::SUCCESS;
    }
}
