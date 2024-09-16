<?php

namespace App\Console\Commands;

use App\Mail\ModeratorExpareApplication;
use App\Models\PdoiApplication;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifySoonExpiredApplications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:soon_expired_applications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify moderators connected to soon expired applications';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info("notify:soon_expired_applications");
        $expiredApplication = PdoiApplication::IsExpireSoon()->get();
        if($expiredApplication->count()){
            foreach ($expiredApplication as $application){
                if($application->responseSubject && $application->responseSubject->users->count()){
                    foreach ($application->responseSubject->users as $user){
                        if(!empty($user->email)){
                            $email = config('app.env') != 'production' ? config('mail.local_to_mail') : $user->email;
                            Mail::to($email)->send(new ModeratorExpareApplication($application, $user));
                            sleep(2);
                        }
                    }
                }
            }
        }
        return Command::SUCCESS;
    }
}
