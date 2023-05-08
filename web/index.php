<?php

use Umb\SystemBackup\Models\Facility;

require_once __DIR__ . "/../vendor/autoload.php";
$facilities = Facility::all(); // TODO filter according to user logged in.
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sharepoint Backups</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="plugins/toastr/toastr.min.css">

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="plugins/sweetalert2/sweetalert2.min.js"></script>

    <!--⚡⚡⚡⚡ Scripts ⚡⚡⚡⚡ -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="plugins/jquery-ui/jquery-ui.min.js"></script>

    <!-- Toastr -->
    <script src="plugins/toastr/toastr.min.js"></script>

</head>

<body>
<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark static-top">
    <div class="container">
        <div class="col-3">
            <div class="form-group">
                <select name="facility" id="selectFacility" class=" form-select">
                    <option value="" selected hidden> Select Facility</option>
                    <?php foreach ($facilities as $facility): ?>
                        <option value="<?php echo $facility->id ?>"><?php echo $facility->name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <a class="navbar-brand" href="#">
            <img src="https://placeholder.pics/svg/150x50/888888/EEE/Logo" alt="..." height="36">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">Upload</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#backups">Backups</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        System Administration
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="#users">Users</a></li>
                        <li><a class="dropdown-item" href="#facilities">Facilities</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#">Something else here</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- nav end -->
<section>
    <?php include_once(__DIR__ . "/upload_file.php") ?>
</section>

<script>

</script>
</body>

</html>