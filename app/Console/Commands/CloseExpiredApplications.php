<?php

namespace App\Console\Commands;

use App\Enums\ApplicationEventsEnum;
use App\Enums\PdoiApplicationStatusesEnum;
use App\Models\PdoiApplication;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class CloseExpiredApplications extends Command
{
    const MAX_TRY = 3;
    const EMAIL_CHANNEL= 1;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:expired_application';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check application with expired end date and expired event "Ask for information" and set correct status (оставено без разглеждане)';

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
        ->where('update_at','<=', $beforeTimestamp)
        ->get();

        if( $notifications->count() ) {
            foreach ($notifications as $item) {
                Mail::to($data['email'])->send(new UsersChangePassword($user));
            }
        }
    }
}

