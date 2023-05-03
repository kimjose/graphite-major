<?php
try {
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


    require('get_token.php');
    $filePath = $fileName;
    $curl = curl_init();


    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://graph.microsoft.com/v1.0/drives/b!0xyf-sxTkkqFel7v-6CHS1h2I9wcc1VItFkBUeMX15rPBkBcpOtiSZVc35A4dA--/items/0167YEQWE5AY7FYDW5ANCKOKICPBWLUIW6:/{$fileName}:/content",
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
} catch (Throwable $th) {
    echo $th->getMessage();
}
