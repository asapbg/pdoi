<?php

namespace App\Console\Commands;

use App\Enums\PdoiSubjectDeliveryMethodsEnum;
use App\Http\Controllers\SsevController;
use Illuminate\Console\Command;

class ResetDeliveryMethodPdoiSubject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pdoi:reset_delivery_method';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset Delivery method for pdoi response subjects and deactivate if no eik and mail';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $subjects = \App\Models\PdoiResponseSubject::get();
        if( $subjects->count() ) {
            foreach ($subjects as $subject) {
                $subject->delivery_method = 0;

                //Deactivate if no email and eik
                if( empty($subject->eik) && empty($subject->email) ) {
                    $subject->active = 0;
                }

                //Set email for delivery method if email
                if( !empty($subject->email) ) {
                    $subject->delivery_method = PdoiSubjectDeliveryMethodsEnum::EMAIL->value;
                }

                //Set ССЕВ for delivery method if ССЕВ profile
                if( !empty($subject->eik) ) {
                    if( SsevController::getEgovProfile($subject->id, $subject->eik) ) {
                        $subject->delivery_method = PdoiSubjectDeliveryMethodsEnum::SDES->value;
                    }
                }
                $subject->save();
            }
        }
        return Command::SUCCESS;
    }
}
