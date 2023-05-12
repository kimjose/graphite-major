<?php

namespace Umb\SystemBackup\Controllers;

use Umb\SystemBackup\Controllers\Utils\Utility;
use Umb\SystemBackup\Models\DriveFile;
use Umb\SystemBackup\Models\Facility;
use Umb\SystemBackup\Models\Upload;

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
                $createdAt = $driveFile->createdDateTime;
                $createdAt = str_replace('T', ' ', $createdAt);
                $createdAt = str_replace('Z', ' ', $createdAt);
                DriveFile::create([
                    'name' => $driveFile->name,
                    'id' => $driveFile->id,
                    'folder_id' => $folderId,
                    'web_url' => $driveFile->webUrl,
                    'download_url' => $downloadUrl,
                    'size' => $driveFile->size,
                    'created_date_time' => $createdAt
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

    public function uploadQueuedFiles()
    {
        try {
            /**
             * Get uploads not sent to sharepoint and loop
             * Check if file exists if not delete the upload
             * if exists get the facility
             * get folder path from facility
             * do upload...↑↑↑↑↑🔼🔼🔼
             * the delete the file locally
             */
            /** @var Upload[] */
            $uploads = Upload::where('uploaded_to_sharepoint', 0)->get();
            foreach ($uploads as $upload) {
                $dir = $_ENV['PUBLIC_DIR'] . 'temp/';
                if (!is_file($dir . $upload->file_name)) {
                    $upload->delete();
                } else {
                    $facility = $upload->facility();
                    if ($facility == null) {
                        $upload->delete();
                        break;
                    }
                    $folderId = $facility->folder_id;
                    $fileName = $upload->file_name;

                    $curl = curl_init();

                    curl_setopt_array($curl, [
                        CURLOPT_URL => "https://graph.microsoft.com/v1.0/drives/b!0xyf-sxTkkqFel7v-6CHS1h2I9wcc1VItFkBUeMX15rPBkBcpOtiSZVc35A4dA--/items/{$folderId}:/{$fileName}:/content",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 600,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "PUT",
                        CURLOPT_POSTFIELDS => file_get_contents($dir . $fileName),
                        CURLOPT_HTTPHEADER => [
                            "Accept: */*",
                            "Authorization: Bearer {$this->accessToken}",
                            "Content-Type: application/octet-stream",
                            "User-Agent: Thunder Client (https://www.thunderclient.com)"
                        ],
                    ]);

                    $response = curl_exec($curl);
                    $err = curl_error($curl);

                    curl_close($curl);

                    if ($err) {
                        throw new \Exception($err, -1);
                    } else {
                        unlink($dir . $upload->file_name);
                        $driveFile = json_decode($response, false);
                        $dstring = "@microsoft.graph.downloadUrl";
                        $downloadUrl = $driveFile->$dstring;
                        $createdAt = $driveFile->createdDateTime;
                        $createdAt = str_replace('T', ' ', $createdAt);
                        $createdAt = str_replace('Z', ' ', $createdAt);
                        DriveFile::create([
                            'name' => $driveFile->name,
                            'id' => $driveFile->id,
                            'folder_id' => $folderId,
                            'web_url' => $driveFile->webUrl,
                            'download_url' => $downloadUrl,
                            'size' => $driveFile->size,
                            'created_date_time' => $createdAt
                        ]);
                        $upload->update(['uploaded_to_sharepoint' => 1]);
                        // Go next
                        // echo $response;
                    }
                }
            }
            response(SUCCESS_RESPONSE_CODE, "Uploaded successfully");
        } catch (\Throwable $th) {
            Utility::logError(SUCCESS_RESPONSE_CODE, $th->getMessage());
            response(PRECONDITION_FAILED_ERROR_CODE, $th->getMessage());
            http_response_code(PRECONDITION_FAILED_ERROR_CODE);
        }
    }
}
