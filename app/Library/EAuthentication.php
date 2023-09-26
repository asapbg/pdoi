<?php

namespace App\Library;

use Carbon\Carbon;
use CkBinData;
use CkCrypt2;
use CkGlobal;
use CkPrivateKey;
use CkRsa;
use CkStringBuilder;
use CkXml;
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
    private string $certificateStr;

    public function __construct()
    {
        $this->endpoint = config('eauth.endpoint');
        $this->certificateStr = str_replace(["-----BEGIN CERTIFICATE-----", "-----END CERTIFICATE-----",  "\r", "\r\n", "\n"], '', file_get_contents(config('eauth.certificate_path')));
    }


    /**
     * Open and auto submit form to IP
     * @param string $source from where is the request (admin/web...type user)
     * @param array $requestParams
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function spLoginPage(string $source = '', array $requestParams = [])
    {
        $xml = $this->generateXml($source);
        $params = array(
            'SAMLRequest' => base64_encode($xml)
        );
        //add additional parameters to form
        if( sizeof($requestParams) ) {
            $params = array_merge($params, $requestParams);
        }

        //load and auto submit form
        return view('eauth.login', compact('params'));
    }


    /**
     * @param $source
     * @return string
     */
    private function generateXml($source):string
    {
        //2023-11-20T11:27:51.265Z
        $callbackUrl = route('eauth.login.callback').(!empty($source) ? '/'.$source : '');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<saml2p:AuthnRequest xmlns:saml2p="urn:oasis:names:tc:SAML:2.0:protocol" AssertionConsumerServiceURL="'.$callbackUrl.'" Destination="'.$this->endpoint.'" ForceAuthn="true" ID="ARQ1a1dd6a-3592-47ab-ae25-5c32dfd91720" IsPassive="false" IssueInstant="'.Carbon::now('UTC')->format('Y-m-d\TH:i:s.v\Z').'" ProtocolBinding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Version="2.0">
  <saml2:Issuer xmlns:saml2="urn:oasis:names:tc:SAML:2.0:assertion">'.route('eauth.sp_metadata').'</saml2:Issuer>
  <saml2p:Extensions>
    <egovbga:RequestedService xmlns:egovbga="urn:bg:egov:eauth:2.0:saml:ext">
      <egovbga:Service>'.config('eauth.service_oid').'</egovbga:Service>
      <egovbga:Provider>'.config('eauth.provider_oid').'</egovbga:Provider>
      <egovbga:LevelOfAssurance>'.$this->levelOfAssurance.'</egovbga:LevelOfAssurance>
    </egovbga:RequestedService>
  </saml2p:Extensions>
</saml2p:AuthnRequest>';
        return $this->sign($xml);
    }

    /**
     * Service provider metadata page
     * @param string $callback_source
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function spMetadata(string $callback_source = ''): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $xml = '<?xml version="1.0"?>
<md:EntityDescriptor xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata" validUntil="'.Carbon::now('UTC')->addYears(1)->format('Y-m-d\TH:i:s\Z').'" cacheDuration="PT604800S" entityID="'.route('eauth.sp_metadata').'">
  <md:SPSSODescriptor AuthnRequestsSigned="true" WantAssertionsSigned="true" protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol">
    <md:KeyDescriptor use="signing">
      <ds:KeyInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
        <ds:X509Data>
          <ds:X509Certificate>'.trim($this->certificateStr).'</ds:X509Certificate>
        </ds:X509Data>
      </ds:KeyInfo>
    </md:KeyDescriptor>
    <md:KeyDescriptor use="encryption">
      <ds:KeyInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
        <ds:X509Data>
          <ds:X509Certificate>'.trim($this->certificateStr).'</ds:X509Certificate>
        </ds:X509Data>
      </ds:KeyInfo>
    </md:KeyDescriptor>
    <md:NameIDFormat>urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified</md:NameIDFormat>
    <md:AssertionConsumerService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="'.$this->endpoint.'" index="1"/>
  </md:SPSSODescriptor>
</md:EntityDescriptor>
';
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
        include(config('eauth.chilkat_library'));

        $glob = new CkGlobal();
        $success = $glob->UnlockBundle('ASAPBG.CB4092025_GvUzdfJg0H2z');
        if ($success != true) {
            Log::error('['.Carbon::now().'] Chilkat License error: '.$glob->lastErrorText());
            return null;
        }

        $status = $glob->get_UnlockStatus();
        if ($status != 2) {
            Log::error('['.Carbon::now().'] Chilkat License expired');
            return null;
        }

        $user = array(
            'email' => null,
            'name' => null,
            'phone' => null,
            'address' => null,
            'legal_form' => null,
            'identity_number' => null,
        );

        $message = $samlResponse ? base64_decode($samlResponse, true) : '';
        if($message && empty($message)) {
            return redirect(route('home'))->with('danger', __('custom.system_error'));
        }

        $xml = new CkXml();
        $xml->LoadXml2($message,true);

        //  Load the RSA private key..
        $privkey = new CkPrivateKey();
        $success = $privkey->LoadPem(file_get_contents('/home/web/ssl/eauth/selfsigned.key'));

        if (!$success) {
            Log::error('['.Carbon::now().'] eAuthentication Error decrypt message: '.$privkey->lastErrorText().PHP_EOL.'Response: '.$message);
            return null;
        }

        //  Prepare an RSA object w/ the private key...
        $rsa = new CkRsa();
        $success = $rsa->ImportPrivateKeyObj($privkey);
        if (!$success) {
            Log::error('['.Carbon::now().'] eAuthentication Error decrypt message: '.$rsa->lastErrorText().PHP_EOL.'Response: '.$message);
            return null;
        }

        //  RSA will be used to decrypt the xenc:EncryptedKey
        //  The bytes to be decrypted are in xenc:CipherValue (in base64 format)
        $encryptedAesKey = $xml->getChildContent('saml2:EncryptedAssertion|xenc:EncryptedData|ds:KeyInfo|xenc:EncryptedKey|xenc:CipherData|xenc:CipherValue');
        if ( !$xml->get_LastMethodSuccess() ) {
            Log::error('['.Carbon::now().'] eAuthentication Error decrypt message: Encrypted AES key not found.'.PHP_EOL.'Response: '.$message);
            return null;
        }

        $bdAesKey = new CkBinData();
        $bdAesKey->AppendEncoded($encryptedAesKey,'base64');

        $sbRsaAlg = new CkStringBuilder();
        $sbRsaAlg->Append($xml->chilkatPath('saml2:EncryptedAssertion|xenc:EncryptedData|ds:KeyInfo|xenc:EncryptedKey|xenc:EncryptionMethod|(Algorithm)'));
        //print 'sbRsaAlg contains: ' . $sbRsaAlg->getAsString() . "\n";
        if ($sbRsaAlg->Contains('rsa-oaep',true) == true) {
            $rsa->put_OaepPadding(true);
        }

        //  Note: The DecryptBd method is introduced in Chilkat v9.5.0.76
        $success = $rsa->DecryptBd($bdAesKey,true);
        if ( !$success ) {
            Log::error('['.Carbon::now().'] eAuthentication Error decrypt message: '.$rsa->lastErrorText().PHP_EOL.'Response: '.$message);
            return null;
        }

        //  Get the encrypted XML (in base64) to be decrypted w/ the AES key.
        $encrypted64 = $xml->getChildContent('saml2:EncryptedAssertion|xenc:EncryptedData|xenc:CipherData|xenc:CipherValue');
        if ( !$xml->get_LastMethodSuccess() ) {
            Log::error('['.Carbon::now().'] eAuthentication Error decrypt message: Encrypted data not found.'.PHP_EOL.'Response: '.$message);
            return null;
        }

        $bdEncrypted = new CkBinData();
        $bdEncrypted->AppendEncoded($encrypted64,'base64');

        //  Get the symmetric algorithm:  "http://www.w3.org/2001/04/xmlenc#aes128-cbc"
        //  and set the symmetric decrypt properties.
        $crypt = new CkCrypt2();
        $crypt->put_Charset('windows-1252');
        $sbAlg = new CkStringBuilder();
        $sbAlg->Append($xml->chilkatPath('saml2:EncryptedAssertion|xenc:EncryptedData|xenc:EncryptionMethod|(Algorithm)'));
        if ( !$sbAlg->Contains('aes128-cbc',true) ) {
            $crypt->put_CryptAlgorithm('aes');
            $crypt->put_KeyLength(128);
            $crypt->put_CipherMode('cbc');
            //  The 1st 16 bytes of the encrypted data are the AES IV.
            $crypt->SetEncodedIV($bdEncrypted->getEncodedChunk(0,16,'hex'),'hex');
            $bdEncrypted->RemoveChunk(0,16);
        }

        //  Other algorithms, key lengths, etc, can be supported by checking for different Algorithm attribute values..
        $crypt->SetEncodedKey($bdAesKey->getEncoded('hex'),'hex');

        //  AES decrypt...
        $success = $crypt->DecryptBd($bdEncrypted);
        if ( !$success ) {
            Log::error('['.Carbon::now().'] eAuthentication Error decrypt message: '.$crypt->lastErrorText().PHP_EOL.'Response: '.$message);
            return null;
        }

        //  Get the decrypted XML
        $decryptedXml = $bdEncrypted->getString('utf-8');

        $xmlAssertion = new CkXml();
        $xmlAssertion->LoadXml($decryptedXml);

        //  Replace the saml2:EncryptedAssertion XML subtree with the saml2:Assertion XML.
        // xmlEncryptedAssertion is a CkXml
        $xmlEncryptedAssertion = $xml->FindChild('saml2:EncryptedAssertion');
        $xmlEncryptedAssertion->SwapTree($xmlAssertion);

        //  The decrypted XML assertion has now replaced the encrypted XML assertion.
        //  Examine the fully decrypted XML document:

        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $xml->getXml());
        $xml = simplexml_load_string(utf8_encode($response));
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
     */
    private function sign($xmlString): string
    {
        file_put_contents('/home/web/sign/test.xml', $xmlString);
        shell_exec('php '.config('eauth.sign_script'));
        sleep(1);
        return file_get_contents('/home/web/sign/signTest.xml');
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
