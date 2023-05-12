const uploadFile = () => {
    let inputFile = document.getElementById("inputFile")
    let progressBar = document.getElementById('progress');
    let progressBarContainer = document.querySelector('.progress-bar')
    let test = $('#progress')
    console.dir(test)

    if (inputFile.files.length < 1) return
    let file = inputFile.files[0]

    let formData = new FormData();
    formData.append('upload_file', file)
    formData.append('system_id', systemId)

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

const addUser = () => {
    let divAddUser = document.getElementById('divAddUser')
    let btnAddUser = document.getElementById('btnAddUser')

    if(divAddUser.classList.contains('d-none')){
        divAddUser.classList.remove('d-none')
        btnAddUser.innerText = 'Close'
    } else{
        divAddUser.classList.add('d-none')
        btnAddUser.innerText = 'Add User'
    }
}

const saveUser = () => {
    let inputLastName = document.querySelector('#inputLastName')
    let inputFirstName = document.querySelector('#inputFirstName')
    let inputMiddleName = document.querySelector('#inputMiddleName')
    let inputPhoneNumber = document.querySelector('#inputPhoneNumber')
    let inputEmail = document.querySelector('#inputEmail')
    let selectAccessLevel = document.querySelector('#selectAccessLevel')
    let btnSaveUser = document.querySelector('#btnSaveUser')

    let lastName = inputLastName.value.trim()
    let firstName = inputFirstName.value.trim()
    let middleName = inputMiddleName.value.trim()
    let phoneNumber = inputPhoneNumber.value.trim()
    let email = inputEmail.value.trim()
    let accessLevel = $(selectAccessLevel).val()
}

