<?php

namespace App\Console\Commands;

use App\Models\PdoiResponseSubject;
use App\Models\RzsSection;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncIisda extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:iisda';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync RZS (Регистър на задължените субекти)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $localSubjects = $toInsert = [];
        //Local subjects
        $dbSubjects = PdoiResponseSubject::select('pdoi_response_subject.id', 'pdoi_response_subject.adm_level', 'pdoi_response_subject.batch_id', 'pdoi_response_subject.nomer_register',
                'pdoi_response_subject.eik', 'pdoi_response_subject.active'
                , 'pdoi_response_subject_translations.subject_name'
                , DB::raw('coalesce(rzs_section.system_name, \'\') as section'))
            ->leftJoin('rzs_section', 'rzs_section.adm_level', '=', 'pdoi_response_subject.adm_level')
            ->leftJoin('pdoi_response_subject_translations', function ($join){
                $join->on('pdoi_response_subject_translations.pdoi_response_subject_id', '=', 'pdoi_response_subject.id')
                    ->where('pdoi_response_subject_translations.locale', '=', 'bg');
            })
            ->where('pdoi_response_subject.adm_register', '=', 1)
            ->get();

        if( $dbSubjects->count() ) {
            foreach ($dbSubjects as $row) {
                $localSubjects[$row->nomer_register] = $row;
            }
        }
        //Local sections
        $localSections = RzsSection::withoutTrashed()->get()->pluck('adm_level', 'system_name')->toArray();

        $data= '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:int="http://iisda.government.bg/RAS/IntegrationServices"><soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing"><wsa:Action>http://iisda.government.bg/RAS/IntegrationServices/IBatchInfoService/SearchBatchesIdentificationInfo</wsa:Action><wsa:To>https://iisda.government.bg/Services/RAS/RAS.Integration.Host/BatchInfoService.svc</wsa:To></soap:Header>
   <soap:Body>
      <int:SearchBatchesIdentificationInfo>
         <int:status>Active</int:status>
      </int:SearchBatchesIdentificationInfo>
   </soap:Body>
</soap:Envelope>';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://iisda.government.bg/Services/RAS/RAS.Integration.Host/BatchInfoService.svc');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/soap+xml',
            'Accept: application/soap+xml',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);

        curl_close($ch);

        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
        $xml = simplexml_load_string($response);
        $json = json_encode($xml);
        $responseArray = json_decode($json, true);
        if( $responseArray ) {
            //sHeader //sBody->SearchBatchesIdentificationInfoResponse->SearchBatchesIdentificationInfoResult->BatchIdentificationInfoType

            if(isset($responseArray['sBody']) && isset($responseArray['sBody']['SearchBatchesIdentificationInfoResponse'])
                && isset($responseArray['sBody']['SearchBatchesIdentificationInfoResponse']['SearchBatchesIdentificationInfoResult'])
                && isset($responseArray['sBody']['SearchBatchesIdentificationInfoResponse']['SearchBatchesIdentificationInfoResult']['BatchIdentificationInfoType'])) {
                $items = $responseArray['sBody']['SearchBatchesIdentificationInfoResponse']['SearchBatchesIdentificationInfoResult']['BatchIdentificationInfoType'];

                DB::beginTransaction();
                try {
                    $updatedCnt = 0;//count how many subject are updated
                    foreach ($items as $row) {
                        if( isset($row['@attributes']) ) {
                            $subject = $row['@attributes'];
                            //TODO fix me add structure if not exist
                            if(!isset($localSections[$subject['AdmStructureKind']])) {
                                Log::error('Sync RZS: Missing AdmStructureKind:'. $subject['AdmStructureKind']);
                                continue;
                            }

//                        "BatchID" => "198"
//                      "Type" => "AdmStructure"
//                      "IdentificationNumber" => "0000000198"
//                      "Name" => "Агенция за държавна финансова инспекция"
//                      "AdmStructureKind" => "ExecutivePowerAdministrativeStructure"
//                      "UIC" => "175076479"
//                      "Status" => "Active"


//                            if(!isset($subject['UIC']) || empty($subject['UIC'])) {
//                                echo 'no EIK';
//                                dd($subject);
//                                  there is a record without eik
//                                "BatchID" => "1442"
//                                "Type" => "AdvisoryBoard"
//                                "IdentificationNumber" => "0000001442"
//                                "Name" => "Акредитационен съвет"
//                                "AdmStructureKind" => "Council"
//                                "Status" => "Active"
//                            }
//                            if(!isset($subject['IdentificationNumber']) || empty($subject['IdentificationNumber'])) {
//                                echo 'no reg nomer';
//                                dd($subject);
//                            }
//                            if(!isset($subject['AdmStructureKind']) || empty($subject['AdmStructureKind'])) {
//                                echo 'no adm level';
//                                dd($subject);
//                            }

                            //if exist in local db check if need update
                            //remove from local subjects array, it means that we found it sync array.
                            // At the end we will deactivate all items not removed from local subject array
                            if( isset($localSubjects[$subject['IdentificationNumber']]) ) {
                                $updated = false;
                                $localSubject = $localSubjects[$subject['IdentificationNumber']];

                                //update subject if need
                                if( (int)$localSubject->batch_id != (int)$subject['BatchID']
                                    || $localSubject->section != $subject['AdmStructureKind']
                                    || $localSubject->eik != ($subject['UIC'] ?? 'N/A')
                                    || ((int)$localSubject->active != (int)($subject['Status'] == 'Active')) ) {

                                    $localSubject->batch_id = (int)$subject['BatchID'];
                                    $localSubject->eik = $subject['UIC'] ?? 'N/A';
                                    $localSubject->adm_level = $localSections[$subject['AdmStructureKind']] ?? 0;
                                    $localSubject->active = (int)($subject['Status'] == 'Active');
                                    $localSubject->save();
                                    $updated = true;
                                }
                                //update translation if need
                                if( $localSubject->subject_name != $subject['Name'] ) {
                                    $localSubject->translate('bg')->subject_name = $subject['Name'];
                                    $localSubject->save();
                                    $updated = true;
                                }
                                unset($localSubjects[$subject['IdentificationNumber']]);
                                if($updated) { $updatedCnt +=1; }
                            } else {
                                $toInsert[] = array(
                                    'batch_id' => $subject['BatchID'],
                                    'eik' => $subject['UIC'] ?? 'N/A',
                                    'nomer_register' => $subject['IdentificationNumber'],
                                    'active' => $subject['IdentificationNumber'] === 'Active',
                                    'adm_level' => $localSections[$subject['AdmStructureKind']] ?? 0,
                                    'subject_name' => $subject['Name'],
                                    'adm_register' => 1
                                );
                            }
                        }
                    }

                    if( sizeof($toInsert) ) {
                        foreach ($toInsert as $newRow) {
                            $newSubject = new PdoiResponseSubject($newRow);
                            $newSubject->save();
                            foreach (config('available_languages') as $lang) {
                                $newSubject->translate($lang['code'])->subject_name = $newRow['subject_name'];
                            }
                            $newSubject->save();
                        }
                    }

                    //deactivate local subject because we did\'t find them in sync array
                    if( sizeof($localSubjects) ) {
                        $idArrayToDeactivate = [];
                        foreach ($localSubjects as $p) {
                            $idArrayToDeactivate[] = $p->id;
                        }
                        PdoiResponseSubject::whereIn('id', $idArrayToDeactivate)->update(['active' => 0]);

                    }

                    echo 'Inserted: '.sizeof($toInsert);
                    echo 'Deactivated: '.sizeof($toInsert);
                    echo 'Updated: '.$updatedCnt;
                    DB::commit();
                    return Command::SUCCESS;

                } catch (\Exception $e){
                    DB::rollBack();
                    Log::error('Sync RZS error: '.$e->getMessage());
                    return Command::FAILURE;
                }

            } else {
                Log::error('Sync RZS: Response array structure missing'. $responseArray);
                return Command::FAILURE;
            }
        } else {
            Log::error('Sync RZS: Unable to parse soap xml response');
            return Command::FAILURE;
        }

//        $f = fopen('result.txt', 'w');
//        fwrite($f,$json);
//        fclose($f);
    }
}
