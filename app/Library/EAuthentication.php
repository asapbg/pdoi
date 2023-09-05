<?php

namespace App\Library;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EAuthentication
{
    /**
     * @var string
     * values: {LOW; SUBSTANTIAL; HIGH}
     */
    private string $levelOfAssurance = 'LOW';

    /** @var string $endpoint */
    private string $endpoint;
    private string $sp_domain;

    /** @var string $xml */
    private string $xml;


    public function __construct()
    {
        $this->endpoint = env('E_AUTH_ENDPOINT_URL', '');
        $this->sp_domain = env('E_AUTH_SP_DOMAIN', '');
    }


    /**
     * Open and auto submit form to IP
     * @param string $source from where is the request (admin/web...type user)
     * @param array $requestParams
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function spLoginPage(string $source = '', array $requestParams = [])
    {
        $this->generateXml($source);
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
    private function generateXml($source)
    {
        //2023-11-20T11:27:51.265Z
        //<saml2:Issuer xmlns:saml2="urn:oasis:names:tc:SAML:2.0:assertion">'.route('eauth.sp_metadata').'</saml2:Issuer>
        $callbackUrl = route('eauth.login.callback').(!empty($source) ? '/'.$source : '');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
        <saml2p:AuthnRequest
           AssertionConsumerServiceURL="'.$callbackUrl.'"
            Destination="'.$this->endpoint.'"
            ForceAuthn="false" ID="ARQ1a1dd6a-3592-47ab-ae25-5c32dfd91720"
            IsPassive="false" IssueInstant="'.Carbon::now('UTC')->format('Y-m-d\TH:i:s.v\Z').'"
            ProtocolBinding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST"
            Version="2.0" xmlns:saml2p="urn:oasis:names:tc:SAML:2.0:protocol">
            <saml2:Issuer xmlns:saml2="urn:oasis:names:tc:SAML:2.0:assertion">'.route('eauth.sp_metadata').(!empty($source) ? '/'.$source : '').'</saml2:Issuer>
            '.$this->signature().'
            <saml2p:Extensions>
                <egovbga:RequestedService xmlns:egovbga="urn:bg:egov:eauth:2.0:saml:ext">
                    <egovbga:Service>'.env('E_AUTH_SERVICE_OID', '').'</egovbga:Service>
                    <egovbga:Provider>'.env('E_AUTH_PROVIDER_OID', '').'</egovbga:Provider>
                    <egovbga:LevelOfAssurance>'.$this->levelOfAssurance.'</egovbga:LevelOfAssurance>
                </egovbga:RequestedService>
            </saml2p:Extensions>
        </saml2p:AuthnRequest>';

        //$this->xml = $this->sign($xml);
        $this->xml = $xml;
    }

    /**
     * Service provider metadata page
     * @param string $callback_source
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function spMetadata(string $callback_source = ''): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $certificateStr = str_replace(['-----BEGIN CERTIFICATE-----', '-----END CERTIFICATE-----'], '', file_get_contents(env('EAUTH_CERT_PATH')));
        $xml = '<EntityDescriptor entityID="'.$this->sp_domain.'" xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata">
                    <SPSSODescriptor WantAssertionsSigned="true" AuthnRequestsSigned="true"
                        protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol" xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata">
                        <md:KeyDescriptor use="signing">
                            <ds:KeyInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
                                <ds:X509Data>
                                <ds:X509Certificate>'.trim($certificateStr).'</ds:X509Certificate>
                                </ds:X509Data>
                            </ds:KeyInfo>
                        </md:KeyDescriptor>
                        <md:KeyDescriptor use="encryption">
                            <ds:KeyInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
                                <ds:X509Data>
                                    <ds:X509Certificate>'.trim($certificateStr).'</ds:X509Certificate>
                                </ds:X509Data>
                            </ds:KeyInfo>
                        </md:KeyDescriptor>
                        <AssertionConsumerService
                            Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST"
                            Location="'.route('eauth.login.callback').(!empty($callback_source) ? '/'.$callback_source : '').'" index="1"/>
                            <AttributeConsumingService index="0" isDefault="true">
                                <ServiceName xml:lang="en">SP</ServiceName>
                                <RequestedAttribute Name="urn:egov:bg:eauth:2.0:attributes:personIdentifier" NameFormat="urn:oasis:names:tc:SAML:2.0:attrname-format:basic" isRequired="true"/>
                                <RequestedAttribute Name="urn:egov:bg:eauth:2.0:attributes:personName" NameFormat="urn:oasis:names:tc:SAML:2.0:attrname-format:basic" isRequired="true"/>
                                <RequestedAttribute Name="urn:egov:bg:eauth:2.0:attributes:email" NameFormat="urn:oasis:names:tc:SAML:2.0:attrname-format:basic" isRequired="true"/>
                                <RequestedAttribute Name="urn:egov:bg:eauth:2.0:attributes:phone" NameFormat="urn:oasis:names:tc:SAML:2.0:attrname-format:basic" isRequired="false"/>
                                <RequestedAttribute Name="urn:egov:bg:eauth:2.0:attributes:dateOfBirth" NameFormat="urn:oasis:names:tc:SAML:2.0:attrname-format:basic" isRequired="false"/>
                                <RequestedAttribute Name="urn:egov:bg:eauth:2.0:attributes:canonicalResidenceAddress" NameFormat="urn:oasis:names:tc:saml2:2.0:attrname-format:uri" isRequired="false"/>
                            </AttributeConsumingService>
                    </SPSSODescriptor>
                </EntityDescriptor>';
        return response($xml, 200, [
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
//                      +"saml2AttributeValue": "PNOBG-1212121212"
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
    private function signature(): string
    {
        $refURI = '#ARQ676c11c-b1d2-49ea-9baf-40e3c7bc7e61';
        //Certificate
        $certificateStr = str_replace(['-----BEGIN CERTIFICATE-----', '-----END CERTIFICATE-----'], '', file_get_contents(env('EAUTH_CERT_PATH')));
        //Signature
        $pk = file_get_contents(env('EAUTH_CERT_PRIVATE_KEY_PATH'));
        // compute signature
        openssl_sign($refURI, $signature, $pk, 'sha256WithRSAEncryption');

        $signature =
'<ds:Signature xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
    <ds:SignedInfo>
        <ds:CanonicalizationMethod Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#" />
        <ds:SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256" />
        <ds:Reference URI="'.$refURI.'">
            <ds:Transforms>
                <ds:Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature" />
                <ds:Transform Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#" />
            </ds:Transforms>
            <ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256" />
            <ds:DigestValue>'.base64_encode(hash('sha256', $refURI)).'</ds:DigestValue>
        </ds:Reference>
    </ds:SignedInfo>
    <ds:SignatureValue>'.base64_encode($signature).'</ds:SignatureValue>
</ds:Signature>';
        return $signature;
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
