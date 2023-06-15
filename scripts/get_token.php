<?php

$curl = curl_init("https://login.microsoftonline.com/1c17770e-a269-4517-b296-c71e84196454/oauth2/v2.0/token");

$postParameter = array(
    'grant_type' => 'client_credentials',
    'client_id' => '391e55cd-5287-4e23-9c8d-4d6917944d12',
    'client_secret' => 'WwN8Q~4jiHGyXqfmTwYRWz2LE3JNixEQymFN_bpe',
    'scope' => 'https://graph.microsoft.com/.default'
);
curl_setopt($curl, CURLOPT_POSTFIELDS, $postParameter);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$curlResponse = json_decode(curl_exec($curl));
// print_r($curlResponse);

$token = $curlResponse->access_token;
//echo $token;

curl_close($curl);
