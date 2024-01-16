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
        $data = [
            '131463734' => [
                'eik' => '131463734',
                'guid' => '{26286351-b7d2-4500-8502-3f7682a6f521}',
                'administrative_body_name' => 'КОМИСИЯ ЗА ОТНЕМАНЕ НА НЕЗАКОННО ПРИДОБИТОТО ИМУЩЕСТВО',
                'phone' => '02 94 01 444',
                'fax' => '02 94 01 595',
                'email' => 'it@ciaf.bg',
                'cert_sn' => '4F3E32E7B01D689B',
                'status' => 1,
                'service' => [
                    'service_name' => 'Документооборот',
                    'uri' => 'https://ciaf.obmen.local:4443/EDocExchangeService',
                    'tip' => 'service',
                    'version' => 1,
                    'status' => 1,
                    'guid' => '{7060efa8-f1fa-4938-8b2b-12a78c86988f}',
                ]
            ],
            '129011056' => [
                'eik' => '129011056',
                'guid' => '{1765bd23-774c-446c-b840-c21584768f33}',
                'administrative_body_name' => 'Комисия за противодействие на корупцията (КПК)',
                'email' => 'cac@cacbg.bg',
                'cert_sn' => '6771DB3941C92AE8',
                'status' => 1,
                'service' => [
                    'service_name' => 'Документооборот',
                    'uri' => 'https://cacbg.obmen.local:2443',
                    'tip' => 'service',
                    'version' => 1,
                    'status' => 1,
                    'guid' => '{7060efa8-f1fa-4938-8b2b-12a78c86988f}',
                ]
            ]
        ];

        foreach ($data as $issdaEik => $item) {
            $subject = \App\Models\PdoiResponseSubject::where('eik', '=', $issdaEik)->first();
            if($subject){
                //Check if egov already exist
                $exist = \App\Models\Egov\EgovOrganisation::withTrashed()->where('eik', '=', $issdaEik)->first();

                if(!$exist){
                    $service = $item['service'] ?? [];
                    if(sizeof($service)) {
                        unset($item['service']);
                        $egovOrganisation = \App\Models\Egov\EgovOrganisation::create($item);
                        if($egovOrganisation) {
                            $service['id_org'] = $egovOrganisation->id;
                            $rgovService = \App\Models\Egov\EgovService::create($service);
                            echo $issdaEik. ' successfully added to egov organisations and services'.PHP_EOL;
                        }
                    }
                } else{
                    echo $issdaEik. ' already exist in egov_organisations'.PHP_EOL;
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
