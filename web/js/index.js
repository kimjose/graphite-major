const uploadFile = () => {
    let inputFile = document.getElementById("inputFile");
    let progressBar = document.getElementById("progress");
    let progressBarContainer = document.querySelector(".progress-bar");
    let test = $("#progress");
    console.dir(test);

    if (inputFile.files.length < 1) return;
    let file = inputFile.files[0];

    let formData = new FormData();
    formData.append("upload_file", file);
    formData.append("system_id", systemId);

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../upload_file", true);
    xhr.upload.onloadstart = (e) => {
        if (progressBarContainer.classList.contains("d-none"))
            progressBarContainer.classList.remove("d-none");
    };
    xhr.upload.onprogress = (e) => {
        if (e.lengthComputable) {
            const percentComplete = (e.loaded / e.total) * 100;
            progressBar.style.width = `${percentComplete}%`;
        }
    };
    xhr.onload = (e) => {
        console.log("On result...");
        if (xhr.status !== 200) {
            toastr.error("Failed to upload file...");
        } else {
            formUploadFile.reset();
            toastr.success("File uploaded successfully");
        }
    };
    xhr.upload.onloadend = (e) => {
        setTimeout(() => {
            if (!progressBarContainer.classList.contains("d-none"))
                progressBarContainer.classList.add("d-none");
        }, 891);
    };
    xhr.send(formData);
};

const addUser = () => {
    let divAddUser = document.getElementById("divAddUser");
    let btnAddUser = document.getElementById("btnAddUser");

    if (divAddUser.classList.contains("d-none")) {
        divAddUser.classList.remove("d-none");
        btnAddUser.innerText = "Close";
    } else {
        divAddUser.classList.add("d-none");
        btnAddUser.innerText = "Add User";
    }
};

const saveUser = () => {
    let inputLastName = document.querySelector("#inputLastName");
    let inputFirstName = document.querySelector("#inputFirstName");
    let inputMiddleName = document.querySelector("#inputMiddleName");
    let inputPhoneNumber = document.querySelector("#inputPhoneNumber");
    let inputEmail = document.querySelector("#inputEmail");
    let selectSystem = document.getElementById('selectSystem')
    let selectAccessLevel = document.querySelector("#selectAccessLevel");
    let btnSaveUser = document.querySelector("#btnSaveUser");

    let lastName = inputLastName.value.trim();
    let firstName = inputFirstName.value.trim();
    let middleName = inputMiddleName.value.trim();
    let phoneNumber = inputPhoneNumber.value.trim();
    let email = inputEmail.value.trim();
    let accessLevel = $(selectAccessLevel).val();
    let systemId = $(selectSystem).val()


    fetch("../user/create", {
        method: "POST",
        body: JSON.stringify({
            email: email,
            access_level: accessLevel,
            phone_number: phoneNumber,
            first_name: firstName,
            middle_name: middleName,
            last_name: lastName
        }),
        headers: {
            "content-type": "application/x-www-form-urlencoded",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            if (response.code === 200) {
                loadTabContent()
            } else throw new Error(response.message);
        })
        .catch((err) => {
            toastr.error(err.message);
        });
};

const accessLevelChanged = () => {
    let divSelectSystem = document.getElementById('divSelectSystem')
    let selectSystem = document.getElementById('selectSystem')
    let selectAccessLevel = document.getElementById('selectAccessLevel')
    let selected = $(selectAccessLevel).val();
    if (selected === 'Program') {
        if (!divSelectSystem.classList.contains('d-none')) divSelectSystem.classList.add('d-none')
        $(selectSystem).val('')
    } else {
        if (divSelectSystem.classList.contains('d-none')) divSelectSystem.classList.remove('d-none')
    }
}

const addSystem = () => {
    let btnAddSystem = document.getElementById("btnAddSystem");
    let divAddSystem = document.getElementById("divAddSystem");

    if (divAddSystem.classList.contains("d-none")) {
        divAddSystem.classList.remove("d-none");
        btnAddSystem.innerText = "Close";
    } else {
        divAddSystem.classList.add("d-none");
        btnAddSystem.innerText = "Add User";
    }
};

const saveSystem = () => {
    let inputName = document.getElementById("inputName");
    let inputFolderId = document.getElementById("inputFolderId");
    let btnSaveSystem = document.getElementById("btnSaveSystem");

    let name = inputName.value.trim();
    if (name == "") {
        toastr.error("name is required");
        inputName.focus();
        return;
    }
    let folderId = inputFolderId.value.trim();
    if (folderId == "") {
        toastr.error("Folder id is required");
        inputFolderId.focus();
        return;
    }

    fetch("../system/create", {
        method: "POST",
        body: JSON.stringify({
            name: name,
            folder_id: folderId,
        }),
        headers: {
            "content-type": "application/x-www-form-urlencoded",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            if (response.code === 200) {
                loadTabContent()
            } else throw new Error(response.message);
        })
        .catch((err) => {
            toastr.error(err.message);
        });
};

const deleteFile = (fileId, folderId) => {
    let r = confirm("Do you really want to delete the file? This action is irreversible")
    if (r){
        fetch(`../sharepoint/path/${folderId}/${fileId}`, {
            method: 'DELETE'
        })
            .then(response =>{
                return response.json()
            })
            .then(response => {
                if(response.code === 200){
                    toastr.success('Deleted successfully.')
                    loadTabContent()
                } else throw new Error(response.message)
            })
            .catch(err => {
                toastr.error(err.message)
            })
    }
}
