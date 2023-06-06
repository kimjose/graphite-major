<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Umb\SystemBackup\Controllers\Utils\Utility;

session_start();
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    $url = "https://";
else
    $url = "http://";
// Append the host(domain name, ip) to the URL.
$url.= $_SERVER['HTTP_HOST'];

// Append the requested resource location to the URL
$url.= $_SERVER['REQUEST_URI'];
$pos = strpos($url, 'web');
$len = strlen($url);
$redirect = substr($url, $pos+strlen('web/'), $len);
$redirect = str_replace('?', '&', $redirect);
if (!isset($_SESSION[$_ENV['SESSION_APP_NAME']])) {
    http_response_code(401);
    Utility::logError(401, "User not authenticated,..");
    header('Location: login?redirect='.$redirect);
//    die(401);
}
$sessionData = $_SESSION[$_ENV['SESSION_APP_NAME']];
if (!isset($sessionData['expires_at'])) {
    http_response_code(401);
    Utility::logError(401, "User not authenticated,..");
    header('Location: login?redirect='.$redirect);
//    die(401);
} else {
    if (time() > $sessionData['expires_at']) {
        session_unset();
        session_destroy();
        http_response_code(401);
        Utility::logError(401, "Session expired");
        header('Location: login?redirect='.$redirect);
//        die(401);
    } else {
        $currUser = $sessionData['user'];
//        if ($currUser->getUserCategory()->events_admin_access != 1){
//            die("You are not allowed to access this service. Consult admin for assistance");
//        }
        $sessionData['expires_at'] = time() + ($_ENV['SESSION_DURATION'] * 60);
        $_SESSION[$_ENV['SESSION_APP_NAME']] = $sessionData;
    }
}




