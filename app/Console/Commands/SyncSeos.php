<?php

namespace App\Console\Commands;

use App\Models\Egov\EgovOrganisation;
use App\Models\Egov\EgovService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Selective\XmlDSig\Algorithm;
use Selective\XmlDSig\CryptoSigner;
use Selective\XmlDSig\PrivateKeyStore;
use Selective\XmlDSig\XmlSigner;

class SyncSeos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:seos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync SEOS information about organizations';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
//        $endpoint = 'https://register.obmen.local/service/';
        $endpoint = config('app.env') == 'production' ? env('SEOS_SYNC_ORGANISATION_ENDPOINT_PROD') : env('SEOS_SYNC_ORGANISATION_ENDPOINT_TEST');
        $soap = '
    <soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
    xmlns:uri="uri:egovmsg">
       <soapenv:Header/>
       <soapenv:Body>
          <uri:GetAllRecords soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>
       </soapenv:Body>
    </soapenv:Envelope>';

        $privateKeyStore = new PrivateKeyStore();
        // load a private key from a string
        $privateKeyStore->loadFromPem(file_get_contents(env('SEOS_SYNC_CERT_PATH', '')), '');
        //Define the digest method: sha1, sha224, sha256, sha384, sha512
        $algorithm = new Algorithm(Algorithm::METHOD_SHA1);
        //Create a CryptoSigner instance:
        $cryptoSigner = new CryptoSigner($privateKeyStore, $algorithm);
        // Create a XmlSigner and pass the crypto signer
        $xmlSigner = new XmlSigner($cryptoSigner);
        // Create a signed XML string
        $soap = $xmlSigner->signXml($soap);
        //var_dump($soap);exit;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: text/xml',
            'Accept: */*',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $soap);
        curl_setopt($ch, CURLOPT_SSLVERSION, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, '1');
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        //certificate
        curl_setopt ($ch, CURLOPT_SSLCERT, env('SEOS_SYNC_CERT_PATH', ''));

        //For test only
//        $err = 0;
//        $code = 200;

        $response = curl_exec($ch);
        $err = curl_error($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err) {
            Log::channel('seos')->info('SEOS soap error (curl):'.PHP_EOL.'Data soap: '.$soap.PHP_EOL. 'Response: '.$response.PHP_EOL.' Response code: '.$code.PHP_EOL.' Error: '.$err);
            return Command::FAILURE;
        } else{
            if( (int)$code != 200 ) {
                Log::channel('seos')->info('SEOS soap error (curl):'.PHP_EOL.'Data soap: '.$soap.PHP_EOL. 'Response: '.$response.PHP_EOL.' Response code: '.$code.PHP_EOL.' Error: '.$err);
                return Command::FAILURE;
            }

            try {

//            $clean_xml = str_ireplace(['S-ENV:', 'S:'], '', $response);
//            $clean_xml = str_ireplace(['SOAP-ENV:', 'S:'], '', $clean_xml);
//            $resp = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $clean_xml);
//            $xml = simplexml_load_string($resp);
//            $json = json_encode($xml);
//            $arrayResponse =  json_decode($json, true);

//            var_dump($arrayResponse);
//            return Command::SUCCESS;

                //Load soap data
                $xml = simplexml_load_string($response);

                // Register namespaces for SOAP and other elements
                $xml->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
                $xml->registerXPathNamespace('ns1', 'uri:egovmsg');
                $xml->registerXPathNamespace('xsi', 'http://www.w3.org/2001/XMLSchema-instance');
                $xml->registerXPathNamespace('tns', 'uri:egovmsg');

                // Extract information for items
                $entities = $xml->xpath('//ns1:GetAllRecordsResponse/return/Entities/item');

                // Loop all items
                foreach ($entities as $entity) {
                    $guid = (string)$entity->Guid;
                    $data = array(
                        'eik' => $entity->EntityIdentifier ? (string)$entity->EntityIdentifier : null,
                        'guid' => $guid,
                        'parent_guid' => $entity->ParentGuid ? (string)$entity->ParentGuid : null,
                        'administrative_body_name' => $entity->AdministrativeBodyName ? (string)$entity->AdministrativeBodyName : null,
                        'postal_address' => null,
                        'web_site' => null,
                        'contact' => null,
                        'phone' => $entity->Contact && $entity->Contact->Phone ? (string)$entity->Contact->Phone : null,
                        'fax' => $entity->Contact && $entity->Contact->Fax ? (string)$entity->Contact->Fax : null,
                        'email' => $entity->Contact && $entity->Contact->EmailAddress ? (string)$entity->Contact->EmailAddress : null,
                        'cert_sn' => $entity->Certificate ? (string)$entity->Certificate : null,
                        'status' => ((string)$entity->Status) == 'Active' ? 1 : 0,
                        'url_http' => null,
                        'url_https' => null,
                    );

                    $egovOrganization = EgovOrganisation::where('guid', '=', $guid)->first();
                    if ($egovOrganization) {
                        $egovOrganization->update($data);
                    } else {
                        $egovOrganization = EgovOrganisation::create($data);
                    }

                    $egovOrganization->refresh();
                    if ($egovOrganization->id) {
                        if (sizeof($entity->Services->item)) {
                            $foundService = 0;
                            foreach ($service = $entity->Services->item as $service){
                                //TODO GUID is the same for all services ?????
                                if(!$foundService && ($service->Name == 'Документооборот' || $service->Name == 'Административен обмен')){
                                    $foundService = 1;
                                    $serviceData = array(
                                        'guid' => (string)$service->Guid,
                                        'service_name' => $service->Name ? (string)$service->Name : null,
                                        'uri' => $service->URI ? (string)$service->URI : null,
                                        'status' => ((string)$service->Status) == 'Active' ? 1 : 0,
                                        'tip' => $service->Type ? (string)$service->Type : null,
                                        'version' => $service->Version ? (string)$service->Version : null,
                                        'selected' => null,
                                    );
                                    $serviceOrganization = EgovService::where('id_org', '=', $egovOrganization->id)->where('guid', '=', (string)$service->Guid)->first();
                                    if ($serviceOrganization) {
                                        $serviceOrganization->update($serviceData);
                                    } else {
                                        $serviceData['id_org'] = $egovOrganization->id;
                                        $serviceOrganization = EgovService::create($serviceData);
                                    }
                                }
                            }

                        }
                    }
                }

                return Command::SUCCESS;
            } catch (\Exception $e){
                Log::channel('seos')->info('Sync SEOS organization error: '.$e);
            }
        }
    }
}