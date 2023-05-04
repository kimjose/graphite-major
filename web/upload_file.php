<?php
$facilityId = $_GET['facility_id'];
?>

<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-body">
            <form action="" method="POST" id="formUploadFile" onsubmit="event.preventDefault()">
                <div class="form-group">
                    <input type="file" name="upload_file" id="inputFile" class="form-control">
                </div>
                <div class="progress-bar">
                    <div id="progress"></div>
                </div>
                <input type="submit" value="Upload" class="btn btn-primary mt-3">

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
        width: 0%;
        height: 100%;
        background-color: #4CAF50;
    }
</style>

<script>
    const inputFile = document.getElementById("inputFile")
    const formUploadFile = document.getElementById("formUploadFile")
    const progressBar = document.getElementById('progress');

    formUploadFile.addEventListener('submit', e => {
        e.preventDefault();

        if (inputFile.files.length < 1) return
        let file = inputFile.files[0]

        let formData = new FormData();
        formData.append('upload_file', file)

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/upload', true);
        xhr.upload.onprogress = e => {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                progressBar.style.width = `${percentComplete}%`;
            }
        };
        xhr.send(formData);
    })
</script>