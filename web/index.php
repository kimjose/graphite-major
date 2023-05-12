<?php

use Umb\SystemBackup\Models\System;

require_once __DIR__ . "/../vendor/autoload.php";
$systems = System::all(); // TODO filter according to user logged in.
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sharepoint Backups</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/adminlte.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="plugins/toastr/toastr.min.css">

    <script src="js/bootstrap.bundle.min.js"></script>

    <!--⚡⚡⚡⚡ Scripts ⚡⚡⚡⚡ -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="plugins/jquery-ui/jquery-ui.min.js"></script>

    <script src="js/adminlte.min.js"></script>
    <!-- Toastr -->
    <script src="plugins/toastr/toastr.min.js"></script>

</head>

<body>
<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark static-top">
    <div class="container">
        <div class="col-3">
            <div class="form-group">
                <select name="system" id="selectSystem" class=" form-select" onchange="systemSelectedChanged()">
                    <option value="" selected hidden> Select System</option>
                    <?php foreach ($systems as $system): ?>
                        <option value="<?php echo $system->id ?>"><?php echo $system->name ?></option>
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
                    <a class="nav-link tab active" id="tabUpload" aria-current="page" href="#" onclick="loadTabContent()">Upload</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link tab" id="tabBackups" href="#backups" onclick="loadTabContent()">Backups</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        System Administration
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item tab" id="tabUsers" href="#users" onclick="loadTabContent()">Users</a></li>
                        <li><a class="dropdown-item tab" id="tabSystems" href="#systems" onclick="loadTabContent()">Systems</a>
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
    const selectSystem = document.getElementById("selectSystem")
    const tabs = document.querySelectorAll(".tab")
    let systemId = '';
    let currentId = window.location.hash

    function init() {
        let selectedSystem = localStorage.getItem('selected_system')
        $(selectSystem).val(selectedSystem)
        loadTabContent()
    }

    function loadTabContent() {
        setTimeout(() => {
            let id = window.location.hash
            console.log(`The id is  ${id}`);
            let selectedSystem = localStorage.getItem('selected_system')
            systemId = selectedSystem;
            $("#contentSection").html('')
            for(let i = 0; i < tabs.length; i++){
                let tab = tabs[i]
                if(tab.classList.contains('active')) tab.classList.remove('active')
            }
            switch (id) {
                case "#backups": {
                    document.querySelector("#tabBackups").classList.add('active')
                    fetch(`backups?system_id=${selectedSystem}`)
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
                case "#users": {
                    document.querySelector("#tabUsers").classList.add('active')
                    fetch(`users`)
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
                case "#systems": {
                    document.querySelector("#tabSystems").classList.add('active')
                    fetch(`systems`)
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
                    document.querySelector("#tabUpload").classList.add('active')
                    fetch(`upload_file?system_id=${selectedSystem}`)
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

    function systemSelectedChanged() {
        let selected = $(selectSystem).val()
        localStorage.setItem('selected_system', selected)
        loadTabContent()
    }

    init()
</script>

<script src="js/index.js"></script>
</body>

</html>