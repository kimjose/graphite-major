<?php

use Bramus\Router\Router;
use Umb\SystemBackup\Controllers\Utils\Utility;

require_once __DIR__ . "/vendor/autoload.php";

$router = new Router();

// Custom 404 Handler
$router->set404(function () {
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    $notFound = file_get_contents("404.html");
    echo $notFound;
});

$router->post('/upload_file', function () {
    try {
        if (isset($_FILES['upload_file'])) {
            $dest = $_ENV['PUBLIC_DIR'] . "temp/";
            if (!is_dir($dest)) {
                mkdir($dest);
            }
            echo Utility::uploadFile("", $dest);
        } else throw new Exception("Error Processing Request", 1);
    } catch (Throwable $th) {
        Utility::logError(-1, $th->getMessage());
        response(PRECONDITION_FAILED_ERROR_CODE, $th->getMessage());
    }
});







$router->all('/logout', function () {
    session_start();
    unset($_SESSION[$_ENV['SESSION_APP_NAME']]);
});

// Thunderbirds are go!
$router->run();
