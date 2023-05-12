<?php
 $systemId = $_GET['system_id'];
?>

<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-body">
            <form action="" method="POST" id="formUploadFile" onsubmit="event.preventDefault()">
                <div class="form-group">
                    <input type="file" name="upload_file" id="inputFile" class="form-control">
                </div>
                <div class="progress-bar mt-2 d-none">
                    <div id="progress" class=""></div>
                </div>
                <input type="submit" value="Upload" class="btn btn-primary mt-3" onclick="uploadFile()">

            </form>
        </div>
    </div>
</div>

<style>
    .progress-bar {
        width: 100%;
        height: 20px;
        background-color: #ddd;
    }

    #progress {
        width: 0;
        height: 100%;
        background-color: #4CAF50;
    }
</style>
