<!DOCTYPE html>
<html>
<head>
    <title>Append into Bash Script</title>
</head>
<body>
    <form action="append_script.php" method="post">
        <textarea name="script_content" rows="10" cols="50"></textarea><br>
        <input type="submit" value="Append and Execute Script">
    </form>
</body>
</html>

<?php

$scriptPath = "/../scripts/install.sh";
$content = $_POST['script_content'];

// Append the content to the script file
file_put_contents($scriptPath, $content, FILE_APPEND);

// Execute the script
$output = shell_exec("bash $scriptPath");

echo "<pre>$output</pre>";


$scriptPath = "/../scripts/install.sh";

$output = shell_exec("bash $scriptPath");

echo $output;
// Insert from textarea
$scriptPath = "/../scripts/install.sh";
$newContent = $_POST['content'];

// Append the new content to the existing script file
file_put_contents($scriptPath, $newContent, FILE_APPEND);

echo "Content added to the script successfully!";
?>
