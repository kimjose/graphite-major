<?php

namespace Umb\SystemBackup\Controllers;

use Umb\SystemBackup\Controllers\Utils\Utility;
use Umb\SystemBackup\Models\DriveFile;
use Umb\SystemBackup\Models\Facility;

class SharepointController
{

    protected $accessToken;

    public function __construct()
    {
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
        if ($curlResponse->error) {
            throw new \Exception("Error Processing Request" . $curlResponse->error, 1);
        }
        $this->accessToken = $curlResponse->access_token;
        //echo $token;

        curl_close($curl);
    }

    public function loadDriveFiles($facilityId)
    {
        try {
            /**
             * Get facility
             * get folder path id : if null ...stop
             * send request
             * read and load response
             */
            $facility = Facility::findOrFail($facilityId);
            $folderId = $facility->folder_id;
            if (!$folderId || $folderId == '') throw new \Exception("Error Processing Request : No folder id", -1);
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => "https://graph.microsoft.com/v1.0/drives/b!0xyf-sxTkkqFel7v-6CHS1h2I9wcc1VItFkBUeMX15rPBkBcpOtiSZVc35A4dA--/items/{$folderId}/children",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => [
                    "Accept: */*",
                    "Authorization: Bearer {$this->accessToken}"
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);
            $jsonData = json_decode($response, false);
            $driveFiles = $jsonData->value;
            DriveFile::where('folder_id', $folderId)->delete();
            // print_r($driveFiles[0]);
            //'id', 'folder_id', 'name', 'web_url', 'download_url', 'size', 'created_date_time'
            foreach ($driveFiles as $driveFile) {
                $dstring = "@microsoft.graph.downloadUrl";
                $downloadUrl = $driveFile->$dstring;
                DriveFile::create([
                    'name' => $driveFile->name,
                    'id' => $driveFile->id,
                    'folder_id' => $folderId,
                    'web_url' => $driveFile->webUrl,
                    'download_url' => $downloadUrl,
                    'size' => $driveFile->size,
                    'created_date_time' => $driveFile->createdDateTime
                ]);
                // echo $downloadUrl;
            }

            curl_close($curl);
        } catch (\Throwable $th) {
            Utility::logError(SUCCESS_RESPONSE_CODE, $th->getMessage());
            response(PRECONDITION_FAILED_ERROR_CODE, $th->getMessage());
            http_response_code(PRECONDITION_FAILED_ERROR_CODE);
        }
    }
}
