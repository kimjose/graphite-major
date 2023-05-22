<?php

use Umb\SystemBackup\Models\System;

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/auth.php";
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
    <!-- Select2 -->
    <link rel="stylesheet" href="plugins/select2/css/select2.min.css">

    <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">


    <script src="js/bootstrap.bundle.min.js"></script>

    <!--⚡⚡⚡⚡ Scripts ⚡⚡⚡⚡ -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="plugins/jquery-ui/jquery-ui.min.js"></script>

    <script src="js/adminlte.min.js"></script>
    <!-- Toastr -->
    <script src="plugins/toastr/toastr.min.js"></script>

    <!-- Select2 -->
    <script src="plugins/select2/js/select2.full.min.js"></script>

    <!-- DataTables  & Plugins -->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>


</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark static-top">
        <div class="container">
            <div class="col-3">
                <div class="form-group">
                    <select name="system" id="selectSystem" class=" form-select" onchange="systemSelectedChanged()">
                        <option value="" selected hidden> Select System</option>
                        <?php foreach ($systems as $system) : ?>
                            <option value="<?php echo $system->id ?>"><?php echo $system->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <a class="navbar-brand" href="#">
                <img src="https://placeholder.pics/svg/150x50/888888/EEE/Logo" alt="..." height="36">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
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
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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
    <div class="content" style="display: flex;">
        <section id="contentSection">
        </section>

        <div class="loader-parent d-none">
            <div class="loader"></div>
        </div>
    </div>
    <style>
        #contentSection {
            width: 100%;
        }

        .loader-parent {
            width: 100%;
            height: 100%;
            z-index: 10;
            background-color: #000020;
            position: absolute;
            cursor: wait;
            /* display: none; */
            opacity: .4;
        }

        .loader {
            border: 16px solid #f3f3f3;
            /* Light grey */
            border-top: 16px solid #3498db;
            /* Blue */
            border-radius: 50%;
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
            margin-left: calc(50% - 60px);
            margin-top: 20%;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
    <script>
        const selectSystem = document.getElementById("selectSystem")
        const tabs = document.querySelectorAll(".tab")
        const loader = document.querySelector(".loader-parent")

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
                for (let i = 0; i < tabs.length; i++) {
                    let tab = tabs[i]
                    if (tab.classList.contains('active')) tab.classList.remove('active')
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
                                $('#tableBackups').dataTable();
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
            $('#select2').select2()
        }

        function systemSelectedChanged() {
            let selected = $(selectSystem).val()
            localStorage.setItem('selected_system', selected)
            loadTabContent()
        }

        function startLoader() {
            if (loader.classList.contains('d-none')) loader.classList.remove('d-none')
        }

        function endLoader() {
            if (!loader.classList.contains('d-none')) loader.classList.add('d-none')
        }

        init()
    </script>

    <script src="js/index.js"></script>
</body>

</html>