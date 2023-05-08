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
                <select name="facility" id="selectFacility" class=" form-select" onchange="facilitySelectedChanged()">
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
                    <a class="nav-link tab active" aria-current="page" href="#" onclick="loadTabContent()">Upload</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link tab" href="#backups" onclick="loadTabContent()">Backups</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        System Administration
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item tab" href="#users" onclick="loadTabContent()">Users</a></li>
                        <li><a class="dropdown-item tab" href="#facilities" onclick="loadTabContent()">Facilities</a>
                        </li>
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
<section id="contentSection">
</section>

<script>
    const selectFacility = document.getElementById("selectFacility")
    const tabs = document.querySelectorAll(".tab")


    function init() {
        let selectedFacility = localStorage.getItem('selected_facility')
        $(selectFacility).val(selectedFacility)
        loadTabContent()
    }

    function loadTabContent() {
        setTimeout(() => {
            let id = window.location.hash
            console.log(`The id is  ${id}`);
            let selectedFacility = localStorage.getItem('selected_facility')

            $("#contentSection").html('')
            switch (id) {
                case "#backups": {
                    fetch(`backups?facility_id=${selectedFacility}`)
                        .then(response => {
                            return response.text()
                        })
                        .then(response => {
                            // console.log('response is ' + response)
                            $("#contentSection").html(response)
                        })
                        .catch(err => {
                            toastr.error(err.message)
                        })
                    break;
                }
                default: {

                    $("#contentSection").html('')
                    fetch(`upload_file?facility_id=${selectedFacility}`)
                        .then(response => {
                            return response.text()
                        })
                        .then(response => {
                            // console.log('response is ' + response)
                            $("#contentSection").html(response)
                        })
                        .catch(err => {
                            toastr.error(err.message)
                        })
                    break
                }
            }
        }, 510)

    }

    function facilitySelectedChanged() {
        let selected = $(selectFacility).val()
        localStorage.setItem('selected_facility', selected)
        loadTabContent()
    }

    init()
</script>

</body>

</html>