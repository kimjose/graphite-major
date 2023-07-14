<?php
require_once 'vendor/autoload.php';

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

$tenantId = $_ENV['TENANT_ID'];
$clientId = $_ENV['CLIENT_ID'];
$clientSecret = $_ENV['CLIENT_SECRET'];

$fileName = 'FILE_NAME';
$groupId = $_ENV['GROUP_ID'];
$teamId= $_ENV['TEAM_ID'];

$graph = new Graph();
$graph->setAccessToken(getAccessToken($tenantId, $clientId, $clientId));

// Retrieve the list of channels in the specified team
$channels = $graph->createRequest("GET", "/teams/{$groupId}/channels")
    ->setReturnType(Model\Channel::class)
    ->execute();

// Print the channel ID and name
foreach ($channels as $channel) {
    echo "Channel ID: " . $channel->getId() . "\n";
    echo "Channel Name: " . $channel->getDisplayName() . "\n\n";
}
$sourceChannelId = getChannelId($graph, $groupId, 'TEST');
$destinationChannelId = getChannelId($graph, $groupId, 'PDW');

echo "The source channel id is : {$sourceChannelId} \n";
echo "The destination channel id is : {$destinationChannelId}";


// Get the source file
/* $sourceDriveItem = $graph->createRequest("GET", "/groups/{$groupId}/drive/items/{$sourceChannelId}:/EVENT MANAGER/CONNECT/KenyaEMR/TEST_HC/enrollments_report.xlsx")
    ->setReturnType(Model\DriveItem::class)
    ->execute(); */
// Get the source folder
$sourceDriveItems = $graph->createRequest("GET", "/groups/{$groupId}/drive/items/{$sourceChannelId}/children")
    ->setReturnType(Model\DriveItem::class)
    ->execute();

foreach ($sourceDriveItems as $sourceDriveItem) {
    if ($sourceDriveItem->folder === null) {
        // Only process files, ignore subfolders

        // Check if the file already exists in the destination channel
        $destinationDriveItem = $graph->createRequest("GET", "/groups/{$groupId}/drive/items/{$destinationChannelId}:/{$sourceDriveItem->name}")
            ->setReturnType(Model\DriveItem::class)
            ->execute();

        // Compare the modified timestamps of the files to determine if syncing is needed
        if ($destinationDriveItem->getLastModifiedDateTime() < $sourceDriveItem->getLastModifiedDateTime()) {
            // File in the destination channel is outdated, update it
            $destinationDriveItem->setContent(null);
            $destinationDriveItem->setFile($sourceDriveItem->getFile());

            $graph->createRequest("PUT", "/groups/{$groupId}/drive/items/{$destinationChannelId}:/{$sourceDriveItem->name}")
                ->attachBody($destinationDriveItem)
                ->execute();

            echo "Synced file: {$sourceDriveItem->name}\n";
        }
    }
}
// Create a new file in the destination channel
$destinationDriveItem = new Model\DriveItem();
$destinationDriveItem->setName($sourceDriveItem->getName());
$destinationDriveItem->setFile($sourceDriveItem->getFile());

$graph->createRequest("PUT", "/groups/{$groupId}/drive/items/{$destinationChannelId}:/CONNECT/KenyaEMR/TEST_HC/enrollments_report.xlsx")
    ->attachBody($destinationDriveItem)
    ->execute();

function getAccessToken($tenantId, $clientId, $clientSecret)
{

    $curl = curl_init("https://login.microsoftonline.com/1c17770e-a269-4517-b296-c71e84196454/oauth2/v2.0/token");

        $postParameter = array(
            'grant_type' => $_ENV['GRANT_TYPE'],
            'client_id' => $_ENV['CLIENT_ID'],
            'client_secret' => $_ENV['CLIENT_SECRET'],
            'scope' => $_ENV['SCOPE']
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postParameter);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $curlResponse = json_decode(curl_exec($curl));
        // print_r($curlResponse);
        if ($curlResponse->error) {
            throw new \Exception("Error Processing Request" . $curlResponse->error, 1);
        }
       // echo "Token is :    " . $curlResponse->access_token . "\n";
        return $curlResponse->access_token;
        //echo $token;

        curl_close($curl);
/*
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
    */
}

function getChannelId($graph, $groupId, $channelName)
{


    // Get the channels in the specified group
    $channels = $graph->createRequest("GET", "/teams/{$groupId}/channels")
        ->setReturnType(\Microsoft\Graph\Model\Channel::class)
        ->execute();



    // Find the channel ID based on the channel name
    $channelId = null;
    foreach ($channels as $channel) {
        if ($channel->getDisplayName() === $channelName) {
            $channelId = $channel->getId();
            break;
        }
    }

    return $channelId;
}
