<?php

namespace App\Api\Ssev;

class EDeliveryAuth
{
    private $scope;
    private $grant_type;
    private $endpoint;
    private $clientId;
    private $oid;
    private $identity;

    function __construct($scope) {
        $this->scope = $scope;
        $this->endpoint = config('e_delivery.endpoint');
        $this->grant_type = config('e_delivery.grant_type');
        $this->clientId = config('e_delivery.client_id');
        $this->oid = config('e_delivery.oid');
        $this->identity = config('e_delivery.identity');
    }

    public function getToken()
    {
        $token = [
            'bearer' => '',
            'miscinfo' => ''
        ];
        $url = $this->endpoint.':5050/token?grant_type='.$this->grant_type.'&client_id='.$this->clientId.'&scope='.$this->scope;
        $response = self::curlRequest($url, [], 'post', ["OID: ".$this->oid, "representedPersonID: ".$this->identity]);
        if( is_array($response) && isset($response['error']) ) {
            echo '['.date('Y-m-d H:i:s').'] Certificate request error: '.PHP_EOL.'Request: '.$url.PHP_EOL.'Error: '.$response['message'];
            return null;
        }

        $jsonResponse = json_decode($response, true);
        if( !$jsonResponse ) {
            echo '['.date('Y-m-d H:i:s').'] Certificate request response error: '.PHP_EOL.'Request: '.$url.PHP_EOL.'Response: '.$response;
            return null;
        }
        if( !isset($jsonResponse['access_token']) ) {
            echo '['.date('Y-m-d H:i:s').'] Certificate request response missing access_token: '.PHP_EOL.'Request: '.$url.PHP_EOL.'Response: '.$response;
            return null;
        }

        $token['bearer'] = $jsonResponse['access_token'];
        //get final token
        $url = $this->endpoint.':5050/introspect?token='.$token['bearer'].'&token_type_hint=access_token';
        $response = self::curlRequest($url, [], 'post');

        if(is_array($response) &&  isset($response['error']) ) {
            echo '['.date('Y-m-d H:i:s').'] Certificate Miscinfo request error: '.PHP_EOL.'Request: '.$url.PHP_EOL.'Error: '.$response['message'];
            return null;
        }

        $jsonResponse = json_decode($response, true);
        if( !$jsonResponse ) {
            echo '['.date('Y-m-d H:i:s').'] Certificate request response error: '.PHP_EOL.'Request: '.$url.PHP_EOL.'Response: '.$response;
            return null;
        }

        if( !isset($jsonResponse['miscinfo']) ) {
            echo '['.date('Y-m-d H:i:s').'] Certificate request response missing miscinfo: '.PHP_EOL.'Request: '.$url.PHP_EOL.'Response: '.$response;
            return null;
        }
        $token['miscinfo'] = $jsonResponse['miscinfo'];
        return $token;
    }

    function curlRequest($url, $requestData = [], $method = 'get', $headers = [])
    {
        $curlHeaders = ["Content-Type: application/soap+xml", "Accept: application/soap+xml"];
        if(sizeof($headers)) {
            foreach ($headers as $h) {
                $curlHeaders[] = $h;
            }
        }

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
//                CURLOPT_VERBOSE => '1',
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            //certificate
            CURLOPT_SSLCERTTYPE => 'p12',
            CURLOPT_SSLCERT => config('e_delivery.client_cert'),
            CURLOPT_SSLKEYPASSWD => config('e_delivery.client_cert_key'),
            CURLOPT_HTTPHEADER => $curlHeaders
        ));

        switch ($method)
        {
            case 'post':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
                break;
            default:
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        }

        $response = curl_exec($ch);
        $err = curl_error($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if( $err ) {
            return array(
                'error' => 1,
                'message' => $err
            );
        }

        if( (int)$code != 200 ) {
            return array(
                'error' => 1,
                'message' => 'code: '.$code
            );
        }

        return $response;
    }
}
