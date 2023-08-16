<?php

namespace App\Library;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Selective\XmlDSig\Algorithm;
use Selective\XmlDSig\CryptoSigner;
use Selective\XmlDSig\PrivateKeyStore;
use Selective\XmlDSig\XmlSigner;

class EAuthentication
{
    /**
     * @var string
     * values: {LOW; SUBSTANTIAL; HIGH}
     */
    private string $levelOfAssurance = 'LOW';

    /** @var string $endpoint */
    private string $endpoint;

    /** @var string $xml */
    private string $xml;


    public function __construct()
    {
        $this->endpoint = env('E_AUTH_ENDPOINT_URL', '');
    }


    /**
     * Open and auto submit form to IP
     * @param array $requestParams
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function spLoginPage(array $requestParams = [])
    {
        $this->generateXml();
        $params = array(
            'SAMLRequest' => base64_encode($this->xml)
        );
        //add additional parameters to form
        if( sizeof($requestParams) ) {
            $params = array_merge($params, $requestParams);
        }

        //load and auto submit form
        return view('eauth.login', compact('params'));
    }


    /**
    * @return void
    */
    private function generateXml()
    {
        //2023-11-20T11:27:51.265Z
        //<saml2:Issuer xmlns:saml2="urn:oasis:names:tc:SAML:2.0:assertion">'.route('eauth.sp_metadata').'</saml2:Issuer>
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
        <saml2p:AuthnRequest
           AssertionConsumerServiceURL="'.route('eauth.login.callback').'"
            Destination="'.$this->endpoint.'"
            ForceAuthn="false" ID="ARQ1a1dd6a-3592-47ab-ae25-5c32dfd91720"
            IsPassive="false" IssueInstant="'.Carbon::now()->toIso8601String().'"
            ProtocolBinding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST"
            Version="2.0" xmlns:saml2p="urn:oasis:names:tc:SAML:2.0:protocol">
            <saml2:Issuer xmlns:saml2="urn:oasis:names:tc:SAML:2.0:assertion">'.route('eauth.sp_metadata').'</saml2:Issuer>
            <saml2p:Extensions>
                <egovbga:RequestedService xmlns:egovbga="urn:bg:egov:eauth:2.0:saml:ext">
                    <egovbga:Service>'.env('E_AUTH_SERVICE_OID', '').'</egovbga:Service>
                    <egovbga:Provider>'.env('E_AUTH_PROVIDER_OID', '').'</egovbga:Provider>
                    <egovbga:LevelOfAssurance>'.$this->levelOfAssurance.'</egovbga:LevelOfAssurance>
                </egovbga:RequestedService>
                <egovbga:RequestedAttributes xmlns:egovbga="urn:bg:egov:eauth:2.0:saml:ext">
                    <egovbga:RequestedAttribute FriendlyName="birthName"
                        Name="urn:egov:bg:eauth:2.0:attributes:birthName"
                        NameFormat="urn:oasis:names:tc:saml2:2.0:attrname-format:uri" isRequired="true">
                        <egovbga:AttributeValue>urn:egov:bg:eauth:2.0:attributes:birthName</egovbga:AttributeValue>
                    </egovbga:RequestedAttribute>
                    <egovbga:RequestedAttribute FriendlyName="email"
                        Name="urn:egov:bg:eauth:2.0:attributes:email"
                        NameFormat="urn:oasis:names:tc:saml2:2.0:attrname-format:uri" isRequired="true">
                        <egovbga:AttributeValue>urn:egov:bg:eauth:2.0:attributes:email</egovbga:AttributeValue>
                    </egovbga:RequestedAttribute>
                    <egovbga:RequestedAttribute FriendlyName="phone"
                        Name="urn:egov:bg:eauth:2.0:attributes:phone"
                        NameFormat="urn:oasis:names:tc:saml2:2.0:attrname-format:uri" isRequired="true">
                        <egovbga:AttributeValue>urn:egov:bg:eauth:2.0:attributes:phone</egovbga:AttributeValue>
                    </egovbga:RequestedAttribute>
                    <egovbga:RequestedAttribute
                        FriendlyName="canonicalResidenceAddress"
                        Name="urn:egov:bg:eauth:2.0:attributes:canonicalResidenceAddress"
                        NameFormat="urn:oasis:names:tc:saml2:2.0:attrname-format:uri" isRequired="true">
                        <egovbga:AttributeValue>urn:egov:bg:eauth:2.0:attributes:canonicalResidenceAddress</egovbga:AttributeValue>
                    </egovbga:RequestedAttribute>
                </egovbga:RequestedAttributes>
            </saml2p:Extensions>
        </saml2p:AuthnRequest>';

        //$this->xml = $this->sign($xml);
        $this->xml = $xml;
    }

    /**
     * Service provider metadata page
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function spMetadata(): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $xml = '<EntityDescriptor entityID="'.route('eauth.sp_metadata').'" xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata">
    <md:SPSSODescriptor WantAssertionsSigned="true"
        protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol" xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata">
        <md:AssertionConsumerService
            Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST"
            Location="'.route('eauth.login.callback').'" index="1"/>
    </md:SPSSODescriptor>
</EntityDescriptor>';
        return response($this->sign($xml), 200, [
            'Content-Type' => 'application/xml'
        ]);
    }

    /**
     * Read and parse IP response for user
     * @param $samlResponse
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|null[]|null
     */
    public function userData($samlResponse)
    {
        $user = array(
            'email' => null,
            'name' => null,
            'phone' => null,
            'address' => null,
            'legal_form' => null,
            'identity_number' => null,
        );

        $message = $samlResponse ? base64_decode($samlResponse) : '';
        if(empty($message)) {
            return redirect(route('home'))->with('danger', __('custom.system_error'));
        }

        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $message);
        $xml = simplexml_load_string($response);
        $json = json_encode($xml);
        $fullMsg = json_decode($json, true);
//        return $fullMsg;
        if( is_null($fullMsg) ) {
            Log::error('['.Carbon::now().'] eAuthentication Invalid response: '.$message);
            return null;
        } else {
            // Check message status
            //        +"saml2pStatus": SimpleXMLElement {#654 ▼
            //            +"saml2pStatusCode": SimpleXMLElement {#678 ▼
            //                +"@attributes": array:1 [▼
            //                "Value" => "urn:oasis:names:tc:SAML:2.0:status:Success"
            //                ]
            //            }
            //        }
            if( !isset($fullMsg['saml2pStatus'])
                || !isset($fullMsg['saml2pStatus']['saml2pStatusCode'])
                || !isset($fullMsg['saml2pStatus']['saml2pStatusCode']['@attributes'])
                || !isset($fullMsg['saml2pStatus']['saml2pStatusCode']['@attributes']['Value']) ) {
                Log::error('['.Carbon::now().'] eAuthentication Missing status information: '.$message);
                return null;
            }

            if( $fullMsg['saml2pStatus']['saml2pStatusCode']['@attributes']['Value'] != 'urn:oasis:names:tc:SAML:2.0:status:Success' ) {
                Log::error('['.Carbon::now().'] eAuthentication Not successful received message: '.$message);
                return null;
            }

            // Get user info
//            +"saml2AttributeStatement": SimpleXMLElement {#674 ▼
//                +"saml2Attribute": array:3 [▼
//                    0 => SimpleXMLElement {#669 ▼
//                                +"@attributes": array:2 [▼
//                        "Name" => "urn:egov:bg:eauth:2.0:attributes:personName"
//                        "NameFormat" => "urn:oasis:names:tc:SAML:2.0:attrname-format:uri"
//                      ]
//                      +"saml2AttributeValue": "MAGDALENA VALERIEVA MITKOVA"
//                    }
//                    1 => SimpleXMLElement {#665 ▼
//                                +"@attributes": array:2 [▼
//                        "Name" => "urn:egov:bg:eauth:2.0:attributes:personIdentifier"
//                        "NameFormat" => "urn:oasis:names:tc:SAML:2.0:attrname-format:uri"
//                      ]
//                      +"saml2AttributeValue": "PNOBG-8001211738"
//                    }
//                    2 => SimpleXMLElement {#664 ▶}
//                                ]
//                            }
//                        dd($fullMsg);
//            }

            if( !isset($fullMsg['saml2Assertion'])
                || !isset($fullMsg['saml2Assertion']['saml2AttributeStatement'])
                || !isset($fullMsg['saml2Assertion']['saml2AttributeStatement']['saml2Attribute'])
                || !is_array($fullMsg['saml2Assertion']['saml2AttributeStatement']['saml2Attribute'])
                || !sizeof($fullMsg['saml2Assertion']['saml2AttributeStatement']['saml2Attribute']) ) {
                Log::error('['.Carbon::now().'] eAuthentication Missing user attributes: '.$message);
                return null;
            }

            foreach ($fullMsg['saml2Assertion']['saml2AttributeStatement']['saml2Attribute'] as $attribute){
                if( isset($attribute['@attributes']) && isset($attribute['@attributes']['Name']) && isset($attribute['saml2AttributeValue']) ) {
                    switch ($attribute['@attributes']['Name']) {
                        case 'urn:egov:bg:eauth:2.0:attributes:personName':
                            $user['name'] = $attribute['saml2AttributeValue'];
                            break;
                        case 'urn:egov:bg:eauth:2.0:attributes:email':
                            $user['email'] = $attribute['saml2AttributeValue'];
                            break;
                        case 'urn:egov:bg:eauth:2.0:attributes:phone':
                            $user['phone'] = $attribute['saml2AttributeValue'];
                            break;
                        case 'urn:egov:bg:eauth:2.0:attributes:canonicalResidenceAddress':
                            $user['address'] = $attribute['saml2AttributeValue'];
                            break;
                        case 'urn:egov:bg:eauth:2.0:attributes:personIdentifier':
                            $identity = $this->parseIdentity($attribute['saml2AttributeValue']);
                            if( isset($identity['legal_form']) && isset($identity['identity_number']) ) {
                                $user['legal_form'] = $identity['legal_form'];
                                $user['identity_number'] = $identity['identity_number'];
                            }
                            break;
                    }
                }
            }
        }
        return $user;
    }

    /**
     * Sign xml
     * @param $xmlString
     * @return string
     */
    private function sign($xmlString): string
    {
        $privateKeyStore = new PrivateKeyStore();
        // load a private key from a string
        $privateKeyStore->loadFromPem(file_get_contents(env('EAUTH_CERT_KEY_PATH')), file_get_contents(env('EAUTH_SERVER_CERT_PATH')));
        //Define the digest method: sha1, sha224, sha256, sha384, sha512
        $algorithm = new Algorithm(Algorithm::METHOD_SHA1);
        //Create a CryptoSigner instance:
        $cryptoSigner = new CryptoSigner($privateKeyStore, $algorithm);
        // Create a XmlSigner and pass the crypto signer
        $xmlSigner = new XmlSigner($cryptoSigner);
        // Create a signed XML string
        return $xmlSigner->signXml($xmlString);
    }

    /**
     * Detect and extract info about legal form and identity number
     * @param $identityString
     * @return array
     */
    private function parseIdentity($identityString): array
    {
        //ПРИМЕР за ЕГН: PNOBG-1010101010 отговаря на физическо лице от българия с ЕГН 1010101010
        //ПРИМЕР за ЕИК: NTRBG-123567896 отговаря на фирма от българия с ЕИК 123567896
        $identity = [];
        if( !empty($identityString) ) {
            $explodeIdentity = explode('-', $identityString);
            if( sizeof($explodeIdentity) == 2 ) {
                if( str_contains($explodeIdentity[0], 'PNO') ) {
                    $identity['legal_form'] = 'person';
                }
                if( str_contains($explodeIdentity[0], 'NTR') ) {
                    $identity['legal_form'] = 'company';
                }
                if( sizeof($identity) ) {
                    $identity['identity_number'] = $explodeIdentity[1];
                }
            }
        }
        return $identity;
    }


}
