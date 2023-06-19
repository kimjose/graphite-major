<?php

use Umb\SystemBackup\Controllers\SharepointController;

require_once __DIR__ . "/../vendor/autoload.php";

try{
    $controller = new SharepointController();
    $controller->uploadQueuedFiles();
} catch(Throwable $th){
    echo "Error encountered " . $th->getMessage();
}
