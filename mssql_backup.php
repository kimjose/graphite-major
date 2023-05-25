
<?php
// sqlcmd
// Database connection details
$server = "your_server_name";
$database = "your_database_name";
$username = "your_username";
$password = "your_password";

// Backup file path and name
$backupPath = "/path/to/backup/directory/";
$backupFileName = "backup_" . date("Y-m-d_H-i-s") . ".bak";

// Construct the backup command
$backupCommand = "sqlcmd -S $server -U $username -P $password -Q \"BACKUP DATABASE [$database] TO DISK='$backupPath$backupFileName'\"";

// Execute the backup command
exec($backupCommand, $output, $returnVar);

// Check if the backup was successful
if ($returnVar === 0) {
    echo "Database backup created successfully.";
} else {
    echo "Error creating database backup.";
}
?>


<?php
// PDOException

// Database credentials
$servername = "localhost";
$username = "your_username";
$password = "your_password";
$database = "your_database";

// Output directory for backups
$backupDir = "path_to_backup_directory/";

// Generate a filename for the backup file
$backupFile = $backupDir . $database . "_" . date("Y-m-d_H-i-s") . ".bak";

// Create a connection to the database
$conn = new PDO("sqlsrv:Server=$servername;Database=$database", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    // SQL query to perform the backup
    $query = "BACKUP DATABASE $database TO DISK = '$backupFile'";

    // Execute the query
    $stmt = $conn->query($query);
    $stmt->execute();

    echo "Database backup created successfully!";
} catch (PDOException $e) {
    echo "Database backup failed: " . $e->getMessage();
}
?>
