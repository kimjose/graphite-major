<?php
// sqlcmd
// Database connection details
$server = "USER";
$database = "CQI_Kenya_BootCamp";
$username = "sa";
$password = "c0nste11a";

// Backup file path and name

$backupFileName = "cqikenya_" . date("Y-m-d_H-i-s") . ".bak";
$backupPath = "C:\IQCareDBBackup\{$backupFileName}";

// Construct the backup command
$backupCommand = "sqlcmd -S $server -U $username -P $password -Q \"BACKUP DATABASE [$database] TO DISK='$backupPath$backupFileName'\"";
//echo $backupCommand;
// Execute the backup command
exec($backupCommand, $output, $returnVar);

// Check if the backup was successful
if ($returnVar === 0) {
    echo "Database backup created successfully.\n";

    echo "Uploading file...";
    require('get_token.php');
    $filePath = "C:\Users\Admin\OneDrive\Documents\openmrs3.docx";
   
    $curl = curl_init();


    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://graph.microsoft.com/v1.0/drives/b!0xyf-sxTkkqFel7v-6CHS1h2I9wcc1VItFkBUeMX15rPBkBcpOtiSZVc35A4dA--/items/0167YEQWEY2XSL3T5RK5DKT74ZCUPQHTYR:/{$backupFileName}:/content",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_POSTFIELDS => file_get_contents($filePath),
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer {$token}"
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response;

} else {
    echo "Error creating database backup.";
}
?>
