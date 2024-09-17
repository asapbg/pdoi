<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(\App\Models\Egov\EgovOrganisation::count()){
            if(!\App\Models\Egov\EgovOrganisation::where('eik', '=', '176986760')->get()->count()){
                $egovOrganizasion = new \App\Models\Egov\EgovOrganisation([
                    'eik' => '176986760',
                    'guid' => '{c9d92a24-2eb7-4f3f-a2e2-0b8939c0ee53}',
                    'parent_guid' => '{196d271a-087f-47ba-bfc6-20d096032270}',
                    'administrative_body_name' => 'Областна дирекция по безопасност на храните – София-град',
                    'phone' => '02/944 46 36',
                    'fax' => '02/944 46 36',
                    'email' => 'rvs_22@nvms.government.bg',
                    'cert_sn' => '1C3B8F7F6B2DA66E',
                    'status' => 1,
                ]);
                $egovOrganizasion->save();
                $egovOrganizasion->refresh();

                if($egovOrganizasion->id){
                    $egovOrganizasion->services()->create([
                        'service_name' => 'Административен обмен v.1',
                        'uri' => 'https://odbh-sof.babh.obmen.local:8422/CommServices/DocExchangeService',
                        'status' => 1,
                        'tip' => 'service',
                        'version' => 1,
                        'guid' => '{7060efa8-f1fa-4938-8b2b-12a78c86988f}',
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
