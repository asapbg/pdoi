<?php
return [
    'local_ssev_profile_id' => env('LOCAL_TO_SSEV_PROFILE_ID', 0),
    'group_ids' => [
        'person' => 1,
        'company' => 2,
        'egov' => 3,
        'article_1_paragraph_2' => 4,
        'marine_group' => 5,
    ],
    'endpoint' => env('SSEV_ENDPOINT', 'test'),
    'grant_type' => env('SSEV_GRANT_TYPE', 'test'),
    'client_id' => env('SSEV_CLIENT_ID', 'test'),
    'oid' => env('SSEV_OID', 'test'),
    'identity' => env('SSEV_IDENTITY', 'test'),
    'client_cert' => env('SSEV_CLIENT_CERT', 'test'),
    'client_cert_key' => env('SSEV_CLIENT_CERT_KEY', 'test'),
];
