<?php
try {
    $backUpDir = "/CRIMS/BACKUP/";
    if (!is_dir($backUpDir)) mkdir($backUpDir);
    $date = date('Y-m-d_His');

    $dbName = "crims_live";
    $dbUser = "dhis";
    $dbPassword = "dhis";

    $fileName = $backUpDir . $dbName . '_' . $date . '.sql';
    $fileNameFinal = $fileName . '.7z';
    $files = scandir($backUpDir);
    for ($i = 2; $i < sizeof($files); $i++) {
        $file = $files[$i];
        $fileDate = substr($file, strlen($dbName) + 1, 10);
        $diff = date_diff(date_create($fileDate), date_create())->days;
        if ($diff > 10) {
            unlink($backUpDir . $file);
        }
    }
    echo " \033[93m  Backing up database and compressing \033[0m ⏳⏳⏳⏳ \n";
    $cmd = "pg_dump --dbname=postgresql://{$dbUser}:{$dbPassword}@127.0.0.1:5432/{$dbName} > {$fileName}";
    $cmd .= " && 7z a -t7z {$fileNameFinal} {$fileName} ";
    $cmd .= " && rm {$fileName}";
//echo $cmd; return;
    //echo 'Executing -> ' . $cmd . " \n";
    if (substr(php_uname(), 0, 7) == "Windows") {
        $r = pclose(popen("start /B " . $cmd, "r"));
    } else {
        exec($cmd . " ");
    }
    echo "\033[96m Backup completed. \033[93m Now uploading file \033[0m {$fileNameFinal} \n ";

    require('get_token.php');
    $filePath = $fileNameFinal;
    $curl = curl_init();


    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://graph.microsoft.com/v1.0/drives/b!0xyf-sxTkkqFel7v-6CHS1h2I9wcc1VItFkBUeMX15rPBkBcpOtiSZVc35A4dA--/items/0167YEQWE5AY7FYDW5ANCKOKICPBWLUIW6:/{$fileNameFinal}:/content",
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