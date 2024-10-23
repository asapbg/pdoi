<?php

namespace App\Console\Commands;

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
        $endpoint = 'https://register.obmen.local/service/';
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

            $clean_xml = str_ireplace(['S-ENV:', 'S:'], '', $response);
            $clean_xml = str_ireplace(['SOAP-ENV:', 'S:'], '', $clean_xml);
            $resp = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $clean_xml);
            $xml = simplexml_load_string($resp);
            $json = json_encode($xml);
            $arrayResponse =  json_decode($json, true);

            var_dump($arrayResponse);
            return Command::SUCCESS;
        }
    }
}
