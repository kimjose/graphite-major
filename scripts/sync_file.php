<?php
require_once 'vendor/autoload.php';

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

$tenantId = 'YOUR_TENANT_ID';
$clientId = 'YOUR_CLIENT_ID';
$clientSecret = 'YOUR_CLIENT_SECRET';

$sourceChannelId = 'SOURCE_CHANNEL_ID';
$destinationChannelId = 'DESTINATION_CHANNEL_ID';
$fileName = 'FILE_NAME';
$groupId = 'GROUP_ID';

$graph = new Graph();
$graph->setAccessToken(getAccessToken());

// Get the source file
$sourceDriveItem = $graph->createRequest("GET", "/groups/{$groupId}/drive/items/{$sourceChannelId}:/{$fileName}")
    ->setReturnType(Model\DriveItem::class)
    ->execute();

// Create a new file in the destination channel
$destinationDriveItem = new Model\DriveItem();
$destinationDriveItem->setName($sourceDriveItem->getName());
$destinationDriveItem->setFile($sourceDriveItem->getFile());

$graph->createRequest("PUT", "/groups/{$groupId}/drive/items/{$destinationChannelId}:/{$fileName}")
    ->attachBody($destinationDriveItem)
    ->execute();

function getAccessToken()
{
    $tenantId = 'YOUR_TENANT_ID';
    $clientId = 'YOUR_CLIENT_ID';
    $clientSecret = 'YOUR_CLIENT_SECRET';

    $url = "https://login.microsoftonline.com/{$tenantId}/oauth2/token";
    $data = [
        'grant_type' => 'client_credentials',
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'resource' => 'https://graph.microsoft.com'
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $result = json_decode($response, true);

    return $result['access_token'];
}
?>
