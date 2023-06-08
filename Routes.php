<?php

use Bramus\Router\Router;
use Umb\SystemBackup\Controllers\SharepointController;
use Umb\SystemBackup\Controllers\UsersController;
use Umb\SystemBackup\Controllers\Utils\Utility;
use Umb\SystemBackup\Models\DriveFile;
use Umb\SystemBackup\Models\System;
use Umb\SystemBackup\Models\Upload;
use Umb\SystemBackup\Models\User;

require_once __DIR__ . "/vendor/autoload.php";

$router = new Router();

// Custom 404 Handler
$router->set404(function () {
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    $notFound = file_get_contents("404.html");
    echo $notFound;
});

$router->mount('/user', function() use($router){
    $router->get('/all', function(){
        response(SUCCESS_RESPONSE_CODE, "Users", User::all());
    });
    $data = json_decode(file_get_contents('php://input'), true);
    $router->post('/create', function() use ($data){
        $controller = new UsersController();
        $controller->createUser($_POST);
    });
    $router->post('/update/{id}', function($id) use ($data){
        $controller = new UsersController();
        $controller->updateUser($id, $data);
    });
    $router->post('/request-otp', function() use($data){
        $controller = new UsersController();
        $controller->requestOtp($data);
    });
    $router->post('/verify-otp', function() use($data){
        $controller = new UsersController();
        $controller->verifyOtp($data);
    });
});

$router->mount('/system', function() use($router){
    $router->get('/all', function(){
        response(SUCCESS_RESPONSE_CODE, "Systems", System::all());
    });
    $data = json_decode(file_get_contents('php://input'), true);
    $router->post('/create', function() use ($data){
        try{
            $attributes = ['name', 'folder_id'];
            $missing = Utility::checkMissingAttributes($data, $attributes);
            throw_if(sizeof($missing) > 0, new \Exception("Missing parameters passed : " . json_encode($missing)));
            $system = System::create($data);
            response(SUCCESS_RESPONSE_CODE, "System", $system);
        } catch(\Throwable $th){
            Utility::logError(SUCCESS_RESPONSE_CODE, $th->getMessage());
            response(PRECONDITION_FAILED_ERROR_CODE, $th->getMessage());
            http_response_code(PRECONDITION_FAILED_ERROR_CODE);
        }
    });
    $router->post('/update/{id}', function($id) use ($data){
        try{
            $attributes = ['name', 'folder_id'];
            $missing = Utility::checkMissingAttributes($data, $attributes);
            throw_if(sizeof($missing) > 0, new \Exception("Missing parameters passed : " . json_encode($missing)));
            $system = System::findOrFail($id);
            $system->update($data);
            response(SUCCESS_RESPONSE_CODE, "System", $system);
        } catch(\Throwable $th){
            Utility::logError(SUCCESS_RESPONSE_CODE, $th->getMessage());
            response(PRECONDITION_FAILED_ERROR_CODE, $th->getMessage());
            http_response_code(PRECONDITION_FAILED_ERROR_CODE);
        }
    });
});
$router->mount('/sharepoint', function() use($router){
    $controller = new SharepointController();
    $router->get('/load_drive_files/{id}', function($id) use ($controller){
        $controller->loadDriveFiles($id);
    });
    $router->get('/upload_queued_files', function() use($controller){
        $controller->uploadQueuedFiles();
    });
    $router->delete('/path/{folder_id}/{id}', function($folder_id, $id) use($controller){
        $controller->deleteFile($folder_id, $id);
    });
    $router->delete('/delete_task/{system_id}', function($system_id) use ($controller){
        $controller->deleteTask($system_id);
    });
});
$router->get('/all_files', function(){
    response(SUCCESS_RESPONSE_CODE, '', DriveFile::all());
});

$router->post('/upload_file', function () {
    try {
        if(!isset($_POST['system_id'])) throw new Exception("Missing attributes: system_id", -1);
        $createdBy = $_POST['created_by'] ?? 1;
        $system = System::findOrFail($_POST['system_id']);
        if (isset($_FILES['upload_file'])) {
            $dest = $_ENV['PUBLIC_DIR'] . "temp/";
            if (!is_dir($dest)) {
                mkdir($dest);
            }

            $file_name = $_FILES['upload_file']['name'];
            $file_name = str_replace(" ", "_", $file_name);
            $file_name = str_replace("/", "_", $file_name);
            $file_name = $system->id . "_" . $file_name;
            $uploaded = Utility::uploadFile($file_name, $dest);
            if($uploaded == '') throw new Exception("Error Processing Request upload", 1);
            Upload::create([
                "system_id" => $system->id, "file_name" => $uploaded, "created_by" => $createdBy
            ]);
        } else throw new Exception("Error Processing Request no file", 1);
    } catch (Throwable $th) {
        Utility::logError(-1, $th->getMessage());
        response(PRECONDITION_FAILED_ERROR_CODE, $th->getMessage());
        http_response_code(PRECONDITION_FAILED_ERROR_CODE);
    }
});

$router->all('/web/logout', function () {
    session_start();
    unset($_SESSION[$_ENV['SESSION_APP_NAME']]);
});

// Thunderbirds are go!
$router->run();
