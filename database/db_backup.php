<?php

use Umb\SystemBackup\Controllers\Utils\Utility;

require_once __DIR__ . '/../vendor/autoload.php';

$backUpDir = $_ENV['DB_BACKUP_DIR'];
try {
    $uploadToSharepoint = true;
    if (!is_dir($backUpDir)) mkdir($backUpDir);
    $date = date('Y-m-d_His');
    $fileName = $backUpDir . $_ENV['DB_NAME'] . '_' . $date . '.sql.gz';
    $files = scandir($backUpDir);
    for ($i = 2; $i < sizeof($files); $i++) {
        $file = $files[$i];
        $fileDate = substr($file, strlen($_ENV['DB_NAME']) + 1, 10);
        $diff = date_diff(date_create($fileDate), date_create())->days;
        if ($diff > 20) {
            unlink($backUpDir . $file);
        }
    }
    $cmd = "mysqldump --single-transaction --quick -u{$_ENV['DB_USER']} -p{$_ENV['DB_PASSWORD']} {$_ENV['DB_NAME']} | gzip > {$fileName}";
    echo 'Executing -> ' . $cmd . " \n";
    if (substr(php_uname(), 0, 7) == "Windows") {
        $r = pclose(popen("start /B " . $cmd, "r"));
    } else {
        exec($cmd . " ");
    }

    if(!$uploadToSharepoint) return;

    $systemId = 58;

    $curl = curl_init();

    $postFields = array(
        "system_id" => $systemId,
        "upload_file" => curl_file_create($fileName)
    );

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://psms.mgickenya.org/system-backup/upload_file",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $postFields,
        CURLOPT_HTTPHEADER => [
            "Accept: */*"
        ],
    ]);
    print_r($curl);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        echo $response;
    }

} catch (\Throwable $th) {
    echo $th->getMessage();
    Utility::logError(-90, $th->getMessage());
}
