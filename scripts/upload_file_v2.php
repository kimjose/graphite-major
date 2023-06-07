<?php

try {

    $systemId = 1;
    $backUpDir = "/CONNECT/KenyaEMR/FacilityName/";
    if (!is_dir($backUpDir)) mkdir($backUpDir);
    $date = date('Y-m-d_His');

    $dbName = "openmrs";
    $dbUser = "root";
    $dbPassword = "test";

    $fileName = $backUpDir . $dbName . '_' . $date . '.sql.gz';
    $files = scandir($backUpDir);
    for ($i = 2; $i < sizeof($files); $i++) {
        $file = $files[$i];
        $fileDate = substr($file, strlen($dbName) + 1, 10);
        $diff = date_diff(date_create($fileDate), date_create())->days;
        if ($diff > 10) {
            unlink($backUpDir . $file);
        }
    }
    $cmd = "mysqldump --single-transaction --quick -u{$dbUser} -p{$dbPassword} {$dbName} | gzip > {$fileName}";
    //echo 'Executing -> ' . $cmd . " \n";
    if (substr(php_uname(), 0, 7) == "Windows") {
        $r = pclose(popen("start /B " . $cmd, "r"));
    } else {
        exec($cmd . " ");
    }


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
} catch (Throwable $th) {
    echo $th->getMessage();
}
