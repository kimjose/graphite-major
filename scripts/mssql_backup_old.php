<?php
// Database credentials
$serverName = "USER"; // SQL Server instance name
$username = "sa"; // SQL Server login username
$password = "c0nste11a"; // SQL Server login password
$databaseName = "CQI_kENYA"; // Database name

// Backup directory
$backupDir = "C:\IQCareDBBackup\CQI";
$backupFilename = $databaseName . "_" . date("Y-m-d_H-i-s") . ".bak";
$backupFilepath = $backupDir . $backupFilename;

// Connect to MSSQL server
$server = new \Microsoft_SQLServer_Driver_SQLSrv_Connection($serverName, array("UID"=>$username, "PWD"=>$password));
if (!$server) {
    die("Connection failed: " . \Microsoft_SQLServer_Driver_SQLSrv_Connection::getLastError());
}

// Connect to database
$db = new \Microsoft_SQLServer_Driver_SQLSrv_Database($databaseName, $server);
if (!$db) {
    die("Error selecting database: " . \Microsoft_SQLServer_Driver_SQLSrv_Connection::getLastError());
}

// Create backup device
$backupDevice = new \Microsoft_SQLServer_Driver_SQLSrv_BackupDevice($backupFilepath, \Microsoft_SQLServer_Driver_SQLSrv_BackupDevice::DISK);

// Create backup object
$backup = new \Microsoft_SQLServer_Driver_SQLSrv_Backup($backupDevice);

// Set backup options
$backup->initialize();
$backup->setOption(\Microsoft_SQLServer_Driver_SQLSrv_Backup::COMPRESSION, true);
$backup->setOption(\Microsoft_SQLServer_Driver_SQLSrv_Backup::INIT, true);

// Backup database
$backup->backup($db);

// Close backup device
$backupDevice->close();

// Print status message
echo "Database backup created: " . $backupFilepath;
?>
