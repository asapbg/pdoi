<?php
return [
    'endpoint' => env('E_AUTH_ENDPOINT_URL', 'test'),
    'sign_script' => env('E_AUTH_SIGN_SCRIPT', 'test'),
    'service_oid' => env('E_AUTH_SERVICE_OID', 'test'),
    'provider_oid' => env('E_AUTH_PROVIDER_OID', 'test'),
    'certificate_path' => env('E_AUTH_CERT_PATH', 'test'),
    'certificate_public_key' => env('E_AUTH_CERT_PUBLIC_KEY_PATH', 'test'),
    'certificate_private_key' => env('E_AUTH_CERT_PRIVATE_KEY_PATH', 'test'),
    'chilkat_library' => env('CHILKAT_LIBRARY', 'test'),
];
