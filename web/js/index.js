const uploadFile = (facilityId) => {
    let inputFile = document.getElementById("inputFile")
    let progressBar = document.getElementById('progress');
    let progressBarContainer = document.querySelector('.progress-bar')
    let test = $('#progress')
    console.dir(test)
    e.preventDefault();

    if (inputFile.files.length < 1) return
    let file = inputFile.files[0]

    let formData = new FormData();
    formData.append('upload_file', file)
    formData.append('facility_id', facilityId)

    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../upload_file', true);
    xhr.upload.onloadstart = e => {
        if (progressBarContainer.classList.contains("d-none")) progressBarContainer.classList.remove("d-none")
    }
    xhr.upload.onprogress = e => {
        if (e.lengthComputable) {
            const percentComplete = (e.loaded / e.total) * 100;
            progressBar.style.width = `${percentComplete}%`;
        }
    };
    xhr.onload = e => {
        console.log('On result...');
        if (xhr.status !== 200) {
            toastr.error("Failed to upload file...")
        } else {
            formUploadFile.reset()
            toastr.success("File uploaded successfully")
        }
    }
    xhr.upload.onloadend = e => {
        setTimeout(() => {
            if (!progressBarContainer.classList.contains("d-none")) progressBarContainer.classList.add("d-none")
        }, 891)
    }
    xhr.send(formData);
}

