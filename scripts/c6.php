<?php

// The version number (9_5_0) should match version of the Chilkat extension used, omitting the micro-version number.
// For example, if using Chilkat v9.5.0.48, then include as shown here:
include(config('eauth.chilkat_library'));

if( !sizeof($argv) || !isset($argv[1]) || empty($argv[1]) ) {
    echo 'No xml to sign'.PHP_EOL;
    exit;
}

//get current application
$xml = $argv[1];

$success = true;
// Load the XML to be signed from a file...
$xmlToSign = new CkXml();
$xmlToSign->LoadXml($xml);

$gen = new CkXmlDSigGen();

$gen->put_SigLocation('saml2p:AuthnRequest');
$gen->put_SigLocationMod(0);
$gen->put_SigNamespacePrefix('');
$gen->put_SigNamespaceUri('http://www.w3.org/2000/09/xmldsig#');
$gen->put_SignedInfoCanonAlg('EXCL_C14N_WithComments');
$gen->put_SignedInfoDigestMethod('sha256');

// -------- Reference 1 --------
$gen->AddSameDocRef('','sha256','EXCL_C14N','','');

// Provide a certificate + private key. (PFX password is test123)
$cert = new CkCert();
$success = $cert->LoadPfxFile('C:\ssl\pitay\eauth\selfsigned.p12','krasig');
//$success = $cert->LoadFromFile('/home/web/ssl/eauth/selfsigned.cer');
if ($success != true) {
    print $cert->lastErrorText() . "\n";
    exit;
}

$gen->SetX509Cert($cert,true);

$gen->put_KeyInfoType('X509Data');
$gen->put_X509Type('IssuerSerial,SubjectName,Certificate');

// Load XML to be signed...
$sbXml = new CkStringBuilder();
$xmlToSign->GetXmlSb($sbXml);

$gen->put_Behaviors('CompactSignedXml');

// Sign the XML...
$success = $gen->CreateXmlDSigSb($sbXml);
if ($success != true) {
    print $gen->lastErrorText() . "\n";
    exit;
}

// -----------------------------------------------

// Save the signed XML to a file.
$success = $sbXml->WriteFile('signTest.xml','utf-8',false);

// ----------------------------------------
// Verify the signatures we just produced...
$verifier = new CkXmlDSig();
$success = $verifier->LoadSignatureSb($sbXml);
if ($success != true) {
    print $verifier->lastErrorText() . "\n";
    exit;
}

$numSigs = $verifier->get_NumSignatures();
$verifyIdx = 0;
while ($verifyIdx < $numSigs) {
    $verifier->put_Selector($verifyIdx);
    $verified = $verifier->VerifySignature(true);
    if ($verified != true) {
        print $verifier->lastErrorText() . "\n";
        exit;
    }

    $verifyIdx = $verifyIdx + 1;
}

print $sbXml->getAsString();

?>
