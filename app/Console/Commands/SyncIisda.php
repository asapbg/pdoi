<?php

namespace App\Console\Commands;

use App\Enums\PdoiSubjectDeliveryMethodsEnum;
use App\Mail\AlertForSubjectChanges;
use App\Models\EkatteArea;
use App\Models\EkatteMunicipality;
use App\Models\EkatteSettlement;
use App\Models\PdoiResponseSubject;
use App\Models\RzsSection;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

    private array $typesToSync = []; //'AdmStructure' //Use to filter which institution to sync
    private array $typesWithAddress = ['AdmStructure']; //Use to filter which institution address we need to get
    private int $getAddressAtOnes = 10;

    public function handle()
    {
        Log::info("Cron run sync:iisda.");

        $localSubjects = $toInsert = $idArrayToDeactivate = [];
        //Local subjects
        $dbSubjects = PdoiResponseSubject::select('pdoi_response_subject.id'
                , 'pdoi_response_subject.adm_level'
                , 'pdoi_response_subject.batch_id'
                , 'pdoi_response_subject.nomer_register'
                ,'pdoi_response_subject.eik', 'pdoi_response_subject.active'
                , 'pdoi_response_subject_translations.subject_name'
                , 'pdoi_response_subject_translations.address'
                , DB::raw('coalesce(rzs_section.system_name, \'\') as section')
                , 'pdoi_response_subject.email'
                , 'pdoi_response_subject.phone'
                , 'pdoi_response_subject.fax'
                , 'pdoi_response_subject.region'
                , 'pdoi_response_subject.municipality'
                , 'pdoi_response_subject.town'
                , 'pdoi_response_subject.zip_code'
                , 'pdoi_response_subject.type'
            )
            ->leftJoin('rzs_section', 'rzs_section.adm_level', '=', 'pdoi_response_subject.adm_level')
            ->leftJoin('pdoi_response_subject_translations', function ($join){
                $join->on('pdoi_response_subject_translations.pdoi_response_subject_id', '=', 'pdoi_response_subject.id')
                    ->where('pdoi_response_subject_translations.locale', '=', 'bg');
            })->when(sizeof($this->typesToSync), function ($query) {
                return $query->where('pdoi_response_subject.type', '=', $this->typesToSync);
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

        //Get list and base info
        $dataSoap= $this->dataSoapSearchBatchesIdentificationInfo();
        $responseArray = $this->getSoap($dataSoap);
        if( $responseArray ) {
            if( isset($responseArray['error']) && $responseArray['error'] ) {
                return Command::FAILURE;
            }
            //sHeader //sBody->SearchBatchesIdentificationInfoResponse->SearchBatchesIdentificationInfoResult->BatchIdentificationInfoType
            if(isset($responseArray['sBody']) && isset($responseArray['sBody']['SearchBatchesIdentificationInfoResponse'])
                && isset($responseArray['sBody']['SearchBatchesIdentificationInfoResponse']['SearchBatchesIdentificationInfoResult'])
                && isset($responseArray['sBody']['SearchBatchesIdentificationInfoResponse']['SearchBatchesIdentificationInfoResult']['BatchIdentificationInfoType'])) {
                $items = $responseArray['sBody']['SearchBatchesIdentificationInfoResponse']['SearchBatchesIdentificationInfoResult']['BatchIdentificationInfoType'];

                if( !sizeof($items) ) {
                    return Command::FAILURE;
                }

                //Get address info
                $responseArrayAddress = $this->getAddressInfo($items);

                DB::beginTransaction();
                try {
                    $updatedCnt = 0;//count how many subject are updated
                    foreach ($items as $row) {
                        if( isset($row['@attributes']) ) {
                            $subject = $row['@attributes'];
                            $addressInfo = $responseArrayAddress[$subject['IdentificationNumber']] ?? null;
                            //TODO fix me add structure if not exist
                            if(!isset($localSections[$subject['AdmStructureKind']])) {
                                $newInstLevel = RzsSection::create([
                                    'system_name' => $subject['AdmStructureKind'],
                                    'manual' => 0
                                ]);
                                if( !$newInstLevel ) {
                                    Log::error('Sync Institution: Missing AdmStructureKind:'. $subject['AdmStructureKind']);
                                    continue;
                                }

                                $newInstLevel->adm_level = $newInstLevel->id;
                                foreach (config('available_languages') as $lang) {
                                    $newInstLevel->translateOrNew($lang['code'])->name = $subject['AdmStructureKind'];
                                }
                                $newInstLevel->save();
                                $localSections[$subject['AdmStructureKind']] = $newInstLevel->id;
                            }

                            //if exist in local db check if need update
                            //remove from local subjects array, it means that we found it in sync array.
                            // At the end we will deactivate all items not removed from local subject array
                            if( isset($localSubjects[$subject['IdentificationNumber']]) ) {
                                $updated = false;
                                $localSubject = $localSubjects[$subject['IdentificationNumber']];

                                //update subject base info if need to
                                if( (int)$localSubject->batch_id != (int)$subject['BatchID']
                                    || $localSubject->section != $subject['AdmStructureKind']
                                    || $localSubject->eik != ($subject['UIC'] ?? 'N/A')
                                    || $localSubject->type != ($subject['Type'] ?? null)
                                    || ((int)$localSubject->active != (int)($subject['Status'] == 'Active'))
                                    || ( $addressInfo && (
                                        $addressInfo['email'] != $localSubject->email
                                        || $addressInfo['phone'] != $localSubject->phone
                                        || $addressInfo['fax'] != $localSubject->fax
                                        || $addressInfo['zip_code'] != $localSubject->zip_code
                                        || $addressInfo['region'] != $localSubject->region
                                        || $addressInfo['municipality'] != $localSubject->municipality
                                        || $addressInfo['town'] != $localSubject->town
                                        )
                                    )
                                ) {
                                    //alert users if change adm_level or status
                                    $newLevel = $localSections[$subject['AdmStructureKind']];
                                    $newStatus = (int)($subject['Status'] == 'Active');
                                    if( $localSubject->adm_level != $newLevel
                                        || $localSubject->active != $newStatus ) {
                                        if( config('app.env') != 'production' ) {
                                            $emailList =[config('mail.local_to_mail')];
                                        } else {
                                            $emailList = $localSubject->getAlertUsersEmail();
                                        }
                                        if( sizeof($emailList) ) {
                                            $mailData = array(
                                                'subject' => $localSubject
                                            );
                                            if( $localSubject->adm_level != $newLevel ) {
                                                $mailData['new_level'] = $newLevel;
                                            }
                                            if( $localSubject->active != $newStatus ) {
                                                $mailData['new_status'] = $newStatus;
                                            }
                                            Mail::to($emailList)->send(new AlertForSubjectChanges($mailData));
                                        }
                                    }

//                                    Log::error('Update base: '.PHP_EOL. $localSubject. PHP_EOL. json_encode($addressInfo));
                                    $localSubject->batch_id = (int)$subject['BatchID'];
                                    $localSubject->eik = $subject['UIC'] ?? 'N/A';
                                    $localSubject->type = $subject['Type'] ?? null;
                                    $localSubject->adm_level = $newLevel;
                                    $localSubject->active = $newStatus;
                                    $localSubject->email = $addressInfo ? $addressInfo['email'] : null;
                                    $localSubject->phone = $addressInfo ? $addressInfo['phone'] : null;
                                    $localSubject->fax = $addressInfo ? $addressInfo['fax'] : null;
                                    $localSubject->zip_code = $addressInfo ? $addressInfo['zip_code'] : null;
                                    $localSubject->region = $addressInfo ? $addressInfo['region'] : null;
                                    $localSubject->municipality = $addressInfo ? $addressInfo['municipality'] : null;
                                    $localSubject->town = $addressInfo ? $addressInfo['town'] : null;
                                    $localSubject->save();
                                    $updated = true;
                                }
                                //update subject translation fields if need to
                                $translationUpdate = false;
                                if( $localSubject->subject_name != $subject['Name'] ) {
//                                    Log::error('Update name: '.PHP_EOL. $localSubject->subject_name. PHP_EOL. $subject['Name']);
                                    foreach (config('available_languages') as $lang) {
                                        $localSubject->translateOrNew($lang['code'])->subject_name = $subject['Name'];
                                    }
                                    $localSubject->translateOrNew('bg')->subject_name = $subject['Name'];
                                    $translationUpdate = true;
                                    $updated = true;
                                }
                                if( $addressInfo && ($localSubject->address != $addressInfo['address']) ) {
//                                    Log::error('Update address: '.PHP_EOL. $localSubject->address. PHP_EOL. $addressInfo['address']);
                                    foreach (config('available_languages') as $lang) {
                                        $localSubject->translateOrNew($lang['code'])->address = $addressInfo['address'];
                                    }
                                    $translationUpdate = true;
                                    $updated = true;
                                }
                                if( $translationUpdate ) {
                                    $localSubject->save();
                                }
                                unset($localSubjects[$subject['IdentificationNumber']]);
                                if($updated) { $updatedCnt +=1; }
                            } else {
                                $toInsert[] = array(
                                    'batch_id' => $subject['BatchID'],
                                    'eik' => $subject['UIC'] ?? 'N/A',
                                    'type' => $subject['Type'] ?? null,
                                    'nomer_register' => $subject['IdentificationNumber'],
                                    'active' => $subject['IdentificationNumber'] === 'Active',
                                    'adm_level' => $localSections[$subject['AdmStructureKind']] ?? 0,
                                    'subject_name' => $subject['Name'],
                                    'adm_register' => 1,
                                    'email' => $addressInfo ? $addressInfo['email'] : null,
                                    'phone' => $addressInfo ? $addressInfo['phone'] : null,
                                    'fax' => $addressInfo ? $addressInfo['fax'] : null,
                                    'zip_code' => $addressInfo ? $addressInfo['zip_code'] : null,
                                    'address' => $addressInfo ? $addressInfo['address'] : null,
                                    'region' => $addressInfo ? $addressInfo['region'] : null,
                                    'municipality' => $addressInfo ? $addressInfo['municipality'] : null,
                                    'town' => $addressInfo ? $addressInfo['town'] : null,
                                    'delivery_method' => PdoiSubjectDeliveryMethodsEnum::EMAIL->value,
                                );
                            }
                        }
                    }

                    if( sizeof($toInsert) ) {
                        foreach ($toInsert as $newRow) {
                            $address = $newRow['address'];
                            unset($newRow['address']);
                            $newSubject = new PdoiResponseSubject($newRow);
                            $newSubject->save();
                            $newSubject->refresh();
                            foreach (config('available_languages') as $lang) {
                                $newSubject->translateOrNew($lang['code'])->subject_name = $newRow['subject_name'];
                                $newSubject->translateOrNew($lang['code'])->address = $address;
                            }
                            $newSubject->save();
                        }
                    }

                    //deactivate local subject because we did\'t find them in sync array
                    if( sizeof($localSubjects) ) {
                        foreach ($localSubjects as $p) {
                            $idArrayToDeactivate[] = $p->id;
                        }
                        PdoiResponseSubject::whereIn('id', $idArrayToDeactivate)->update(['active' => 0]);

                    }

                    echo 'Inserted: '.sizeof($toInsert);
                    echo 'Deactivated: '.sizeof($idArrayToDeactivate);
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

    }

    private function getAddressInfo($items): array
    {
        $response = [];
        if (sizeof($items) ) {
            $areas = EkatteArea::select('id', 'oblast')->get()->pluck('id', 'oblast')->toArray();
            $municipalities = EkatteMunicipality::select('id', 'obstina')->get()->pluck('id', 'obstina')->toArray();
            $settlements = EkatteSettlement::select('id', 'ekatte')->get()->pluck('id', 'ekatte')->toArray();

            $chunks = array_chunk($items, $this->getAddressAtOnes);
            if( sizeof($chunks) ) {
                foreach ($chunks as $chunk) {
                    $dataSoap = $this->dataSoapGetBatchDetailedInfo($chunk);
                    if(empty($dataSoap)) {continue;}

                    $responseArray = $this->getSoap($dataSoap);

                    if( isset($responseArray['error']) && $responseArray['error'] ) {
                        continue;
                    }
                    if(isset($responseArray['sBody']) && isset($responseArray['sBody']['GetBatchDetailedInfoResponse'])
                        && isset($responseArray['sBody']['GetBatchDetailedInfoResponse']['GetBatchDetailedInfoResult'])
                        && isset($responseArray['sBody']['GetBatchDetailedInfoResponse']['GetBatchDetailedInfoResult']['BatchType'])) {

                        $soapItems = $responseArray['sBody']['GetBatchDetailedInfoResponse']['GetBatchDetailedInfoResult']['BatchType'];
                        if( sizeof($soapItems) ) {
                            foreach ($soapItems as $item) {
                                if( isset($item['@attributes']) && isset($item['@attributes']['IdentificationNumber'])) {
//                                    if(!isset($item['@attributes']['IdentificationNumber'])) {
//                                        dd($item, $item['@attributes']);
//                                    }
                                    $responseKey = $item['@attributes']['IdentificationNumber'];
                                    $response[$responseKey] = [
                                        'email' => null,
                                        'phone' => null,
                                        'fax' => null,
                                        'zip_code' => null,
                                        'address' => null,
                                        'region' => null,
                                        'municipality' => null,
                                        'town' => null,
                                    ];

                                    if( isset($item['Administration']) ) {
                                        if( isset($item['Administration']['CorrespondenceData']) && isset($item['Administration']['CorrespondenceData']['@attributes']) ) {
                                            $correspondenceData = $item['Administration']['CorrespondenceData']['@attributes'];

                                            //email
                                            if( isset($correspondenceData['Email']) ) {
                                                $response[$responseKey]['email'] = $correspondenceData['Email'];
                                            }
                                            //phone code
                                            $phoneCode = isset($correspondenceData['InterSettlementCallingCode']) && !empty(trim($correspondenceData['InterSettlementCallingCode'])) ?
                                                '('.$correspondenceData['InterSettlementCallingCode'].')' : '';
                                            //fax
                                            if( isset($correspondenceData['FaxNumber']) ) {
                                                $response[$responseKey]['fax'] = $phoneCode.$correspondenceData['FaxNumber'];
                                            }
                                        }
                                        //phone
                                        if( isset($item['Administration']['CorrespondenceData']) && isset($item['Administration']['CorrespondenceData']['Phone']) ) {
                                            $phonesData = $item['Administration']['CorrespondenceData']['Phone'];
                                            foreach ($phonesData as $phone) {
                                                if( isset($phone['PhoneNumber']) ) {
                                                    $response[$responseKey]['phone'] .= ($phoneCode ?? '').$phone['PhoneNumber'].';';
                                                }
                                            }
                                        }
                                        //address
                                        if( isset($item['Administration']['Address']) && isset($item['Administration']['Address']['@attributes']) ) {
                                            $addressPart1 = $item['Administration']['Address']['@attributes'];
                                            if( isset($addressPart1['PostCode']) ) {
                                                $response[$responseKey]['zip_code'] = $addressPart1['PostCode'];
                                            }
                                            if( isset($addressPart1['AddressText']) ) {
                                                $response[$responseKey]['address'] = str_replace('"', '', $addressPart1['AddressText']);
                                            }
                                        }
                                        //ekatte
                                        if( isset($item['Administration']['Address']) && isset($item['Administration']['Address']['EkatteAddress'])
                                            && isset($item['Administration']['Address']['EkatteAddress']['@attributes']) ) {
                                            $addressPart2 = $item['Administration']['Address']['EkatteAddress']['@attributes'];
//                                            dd($addressPart2, $areas);
                                            if( isset($addressPart2['DistrictEkatteCode']) ) {
                                                $response[$responseKey]['region'] = $areas[$addressPart2['DistrictEkatteCode']] ?? null;
                                            }
                                            if( isset($addressPart2['MunicipalityEkatteCode']) ) {
                                                $response[$responseKey]['municipality'] = $municipalities[$addressPart2['MunicipalityEkatteCode']] ?? null;
                                            }
                                            if( isset($addressPart2['SettlementEkatteCode']) ) {
                                                $response[$responseKey]['town'] = $settlements[$addressPart2['SettlementEkatteCode']] ?? null;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $response;
    }

    private function dataSoapSearchBatchesIdentificationInfo(): string
    {
        $types = '';
        if( sizeof($this->typesToSync) ) {
            foreach ($this->typesToSync as $type) {
                $types .= '<int:batchType>'.$type.'</int:batchType>';
            }
        }
        return '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:int="http://iisda.government.bg/RAS/IntegrationServices"><soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing"><wsa:Action>http://iisda.government.bg/RAS/IntegrationServices/IBatchInfoService/SearchBatchesIdentificationInfo</wsa:Action><wsa:To>https://iisda.government.bg/Services/RAS/RAS.Integration.Host/BatchInfoService.svc</wsa:To></soap:Header>
   <soap:Body>
      <int:SearchBatchesIdentificationInfo>
         <int:status>Active</int:status>
         '.$types.'
      </int:SearchBatchesIdentificationInfo>
   </soap:Body>
</soap:Envelope>';
    }

    private function dataSoapGetBatchDetailedInfo($items): string
    {
        $dataSoap = $itemsToSearch = '';
        foreach ($items as $row) {
            if (isset($row['@attributes']) && isset($row['@attributes']['Type'])
                && isset($row['@attributes']['IdentificationNumber']) && in_array($row['@attributes']['Type'], $this->typesWithAddress) ) {
                $itemsToSearch .= '<int:string>'. $row['@attributes']['IdentificationNumber'] .'</int:string>';
            }
        }

        if( !empty($itemsToSearch) ) {
            $dataSoap = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:int="http://iisda.government.bg/RAS/IntegrationServices"><soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing"><wsa:Action>http://iisda.government.bg/RAS/IntegrationServices/IBatchInfoService/GetBatchDetailedInfo</wsa:Action><wsa:To>https://iisda.government.bg/Services/RAS/RAS.Integration.Host/BatchInfoService.svc</wsa:To></soap:Header>
                       <soap:Body>
                          <int:GetBatchDetailedInfo>
                             <int:batchIdentificationNumber>'. $itemsToSearch .'</int:batchIdentificationNumber>
                  </int:GetBatchDetailedInfo>
               </soap:Body>
            </soap:Envelope>';
        }

        return $dataSoap;
    }

    private function getSoap($data){
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
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $response = curl_exec($ch);
        $err = curl_error($ch);

        curl_close($ch);

        if ($err) {
            Log::error('Sync RZS error (curl):'.PHP_EOL.'Data soap: '.$data.PHP_EOL. 'Error: '.$err);
            return ['error' => 1];
        } else{
            $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
            $xml = simplexml_load_string($response);
            $json = json_encode($xml);

            return json_decode($json, true);
        }
    }
}
