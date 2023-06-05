<?php

use Umb\SystemBackup\Controllers\SharepointController;
use Umb\SystemBackup\Models\System;

require_once __DIR__ . "/../vendor/autoload.php";

try {
    $systems = System::all();
    $controller = new SharepointController();
    foreach($systems as $system){
        $controller->deleteTask($system->id);
    }
} catch (Throwable $th) {
    echo "Error encountered " . $th->getMessage();
}
