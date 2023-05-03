<?php
require "vendor/autoload.php";
if (!file_exists(__DIR__ . '/.env')) {
    echo "Unable to load configurations file.";
    http_response_code(412);
    return;
}

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Validate environment variables
$envVars = [
    'SESSION_APP_NAME', 'SESSION_DURATION', 'PUBLIC_DIR', 'LOGS_DIR', 'DB_BACKUP_DIR', 'APP_NAME', 'DB_HOST', 'DB_DRIVER', 'DB_USER', 'DB_PASSWORD',
    'DB_NAME', 'MAILER_NAME', 'MAILER_HOST', 'MAILER_ADDRESS', 'MAILER_PASSWORD', 'MAILER_PORT'
];
$unsetVars = [];
foreach ($envVars as $envVar) {
    if (!isset($_ENV[$envVar])) $unsetVars[] = $envVar;
}
if (sizeof($unsetVars) > 0) {
    die("<h4>Unable to proceed. Environment variables not set. <h4>" . json_encode($unsetVars));
}

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;
$capsule->addConnection([
    "driver" => $_ENV["DB_DRIVER"],
    "host" => $_ENV["DB_HOST"],
    "database" => $_ENV["DB_NAME"],
    "username" => $_ENV["DB_USER"],
    "password" => $_ENV["DB_PASSWORD"]
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

//CONSTANTs
define("SUCCESS_RESPONSE_CODE", 200);
define("PRECONDITION_FAILED_ERROR_CODE", 412);
define("FORBIDDEN_RESPONSE_CODE", 403);
//Permissions
define("PERM_SYSTEM_ADMINISTRATION", 1);
define("PERM_USER_MANAGEMENT", 2);
define("PERM_CHECKLIST_MANAGEMENT", 3);
define("PERM_ACCESS_REPORT", 4);
define("PERM_CREATE_VISIT", 5);
define("PERM_ALL_APS", 5);


