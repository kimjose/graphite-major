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
  formData.append("created_by", user.id);

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
  let inputUserId = document.querySelector("#inputUserId");

  if (divAddUser.classList.contains("d-none")) {
    divAddUser.classList.remove("d-none");
    inputUserId.value = "";
    btnAddUser.innerText = "Close";
  } else {
    document.getElementById("formUser").reset();
    selectAccessLevel.dispatchEvent(new Event("change"));
    let selectSystem = document.getElementById("selectSystemForm");
    let options = selectSystem.options;
    $(selectSystem).select2("destroy");
    for (let i = 0; i < options.length; i++) {
      let option = options[i];
      option.selected = false;
    }
    $(selectSystem).select2();
    divAddUser.classList.add("d-none");
    btnAddUser.innerText = "Add User";
  }
};

const editUser = (user) => {
  let divAddUser = document.getElementById("divAddUser");
  let btnAddUser = document.getElementById("btnAddUser");
  let inputUserId = document.querySelector("#inputUserId");
  let inputLastName = document.querySelector("#inputLastName");
  let inputFirstName = document.querySelector("#inputFirstName");
  let inputMiddleName = document.querySelector("#inputMiddleName");
  let inputPhoneNumber = document.querySelector("#inputPhoneNumber");
  let inputEmail = document.querySelector("#inputEmail");
  let selectSystem = document.getElementById("selectSystemForm");
  let selectAccessLevel = document.querySelector("#selectAccessLevel");

  if (divAddUser.classList.contains("d-none")) {
    divAddUser.classList.remove("d-none");
    btnAddUser.innerText = "Close";
  }
  inputUserId.value = user.id;
  inputLastName.value = user.last_name;
  inputFirstName.value = user.first_name;
  inputMiddleName.value = user.middle_name;
  inputPhoneNumber.value = user.phone_number;
  inputEmail.value = user.email;
  $(selectAccessLevel).val(user.access_level);
  selectAccessLevel.dispatchEvent(new Event("change"));
  if (user.access_level === "Facility") {
    let systems = user.system_ids.split(",");
    let options = selectSystem.options;

    if ($(selectSystem).data("select2")) {
      // $(selectSystem).select2("destroy");
    }
    $(selectSystem).select2("destroy");
    console.log(systems);
    for (let i = 0; i < options.length; i++) {
      let option = options[i];
      let optValue = option.value;
      console.log(`The value is ${optValue}`);
      let indexOf = systems.indexOf(optValue);
      console.log(`The index of is ${indexOf}`);
      if (indexOf != -1) option.selected = true;
      else option.selected = false;
      console.log("Is selected " + option.selected);
    }
    console.dir(selectSystem.options);
    $(function () {
      $(selectSystem).select2();
    });
  } else {
    let options = selectSystem.options;
    $(selectSystem).select2("destroy");
    for (let i = 0; i < options.length; i++) {
      let option = options[i];
      option.selected = false;
    }
    $(selectSystem).select2();
  }
};

const saveUser = () => {
  let inputUserId = document.querySelector("#inputUserId");
  let inputLastName = document.querySelector("#inputLastName");
  let inputFirstName = document.querySelector("#inputFirstName");
  let inputMiddleName = document.querySelector("#inputMiddleName");
  let inputPhoneNumber = document.querySelector("#inputPhoneNumber");
  let inputEmail = document.querySelector("#inputEmail");
  let selectSystem = document.getElementById("selectSystemForm");
  let selectAccessLevel = document.querySelector("#selectAccessLevel");
  let btnSaveUser = document.querySelector("#btnSaveUser");

  let id = inputUserId.value.trim();
  let lastName = inputLastName.value.trim();
  if (lastName === "") {
    toastr.error("Last name is required");
    inputLastName.focus();
    return;
  }
  let firstName = inputFirstName.value.trim();
  if (firstName === "") {
    toastr.error("First name is required");
    inputFirstName.focus();
    return;
  }
  let middleName = inputMiddleName.value.trim();
  if (middleName === "") {
    toastr.error("Last name is required");
    inputMiddleName.focus();
    return;
  }
  let phoneNumber = inputPhoneNumber.value.trim();
  if (phoneNumber.length < 10) {
    toastr.error("Invalid phone number");
    inputPhoneNumber.focus();
    return;
  }
  let email = inputEmail.value.trim();
  if (lastName === "") {
    toastr.error("Last name is required");
    inputLastName.focus();
    return;
  }
  let accessLevel = $(selectAccessLevel).val();
  if (accessLevel === "") {
    toastr.error("Access level is required");
    selectAccessLevel.focus();
    return;
  }
  let systemIds = [];

  let formData = new FormData(document.getElementById("formUser"));
  console.log(formData);

  startLoader();
  $.ajax({
    url: id === "" ? "../user/create" : `../user/update/${id}`,
    data: formData,
    cache: false,
    contentType: false,
    processData: false,
    method: "POST",
    type: "POST",
    success: function (resp) {
      endLoader();
      if (resp.code == 200) {
        toastr.success(resp.message);
        setTimeout(function () {
          loadTabContent();
        }, 800);
      } else {
        endLoader();
        toastr.error(resp.message);
      }
    },
    error: function (request, status, error) {
      endLoader();
      toastr.error(request.responseText);
    },
  });

  let options = selectSystem.querySelectorAll("option");
  for (let i = 0; i < options.length; i++) {
    let option = options[i];
    console.dir(option);
    if (option.selected) {
      systemIds.push(option.value);
    }
  }
};

const accessLevelChanged = () => {
  let divSelectSystem = document.getElementById("divSelectSystem");
  let selectSystem = document.getElementById("selectSystemForm");
  let selectAccessLevel = document.getElementById("selectAccessLevel");
  let selected = $(selectAccessLevel).val();
  if (selected === "Program") {
    if (!divSelectSystem.classList.contains("d-none"))
      divSelectSystem.classList.add("d-none");
    $(selectSystem).val("");
  } else {
    if (divSelectSystem.classList.contains("d-none"))
      divSelectSystem.classList.remove("d-none");
  }
};

const addSystem = () => {
  let btnAddSystem = document.getElementById("btnAddSystem");
  let divAddSystem = document.getElementById("divAddSystem");
  let inputId = document.getElementById("inputId");

  if (divAddSystem.classList.contains("d-none")) {
    inputId.value = "";
    divAddSystem.classList.remove("d-none");
    btnAddSystem.innerText = "Close";
  } else {
    document.getElementById("formSystem").reset();
    divAddSystem.classList.add("d-none");
    btnAddSystem.innerText = "Add System";
  }
};

const editSystem = (id, name, folderId) => {
  let btnAddSystem = document.getElementById("btnAddSystem");
  let divAddSystem = document.getElementById("divAddSystem");
  let inputId = document.getElementById("inputId");
  let inputName = document.getElementById("inputName");
  let inputFolderId = document.getElementById("inputFolderId");

  if (divAddSystem.classList.contains("d-none")) {
    divAddSystem.classList.remove("d-none");
    btnAddSystem.innerText = "Close";
  } else {
    divAddSystem.classList.add("d-none");
    btnAddSystem.innerText = "Add User";
  }
  inputId.value = id;
  inputName.value = name;
  inputFolderId.value = folderId;
};

const saveSystem = () => {
  let inputId = document.getElementById("inputId");
  let inputName = document.getElementById("inputName");
  let inputFolderId = document.getElementById("inputFolderId");
  let btnSaveSystem = document.getElementById("btnSaveSystem");

  let _id = inputId.value.trim();
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

  startLoader();
  fetch(_id == "" ? "../system/create" : `../system/update/${_id}`, {
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
      endLoader();
      if (response.code === 200) {
        loadTabContent();
      } else throw new Error(response.message);
    })
    .catch((err) => {
      endLoader();
      toastr.error(err.message);
    });
};

const toggleGetFolderId = () => {
  let divGetFolderId = document.getElementById("divGetFolderId");
  console.log("Clicked...");
  if (divGetFolderId.classList.contains("d-none")) {
    divGetFolderId.classList.remove("d-none");
  } else {
    divGetFolderId.classList.add("d-none");
  }
};

const getFolderId = () => {
  let inputFolderPath = document.getElementById("inputFolderPath");
  let inputFolderId = document.getElementById("inputFolderId");
  let path = inputFolderPath.value.trim();
  if (path == "") {
    toastr.error("Enter a valid path.");
    return;
  }
  toastr.info("Getting ID...");
  fetch(`../sharepoint/folder_id?path=${path}`)
    .then((response) => {
      return response.json();
    })
    .then((response) => {
      if (response.code == 200) {
        toastr.success(response.message);
        let data = response.data;
        inputFolderId.value = data.id;
      } else throw new Error(response.message);
    })
    .catch((err) => {
      toastr.error(err.message);
    });
};

const deleteFile = (fileId, folderId) => {
  let r = confirm(
    "Do you really want to delete the file? This action is irreversible"
  );
  if (r) {
    startLoader();
    fetch(`../sharepoint/path/${folderId}/${fileId}`, {
      method: "DELETE",
    })
      .then((response) => {
        return response.json();
      })
      .then((response) => {
        endLoader();
        if (response.code === 200) {
          toastr.success("Deleted successfully.");
          loadTabContent();
        } else throw new Error(response.message);
      })
      .catch((err) => {
        endLoader();
        toastr.error(err.message);
      });
  }
};

const reloadBackups = () => {
  startLoader();
  fetch(`../sharepoint/load_drive_files/${systemId}`, {
    method: "GET",
  })
    .then((response) => {
      return response.json();
    })
    .then((response) => {
      endLoader();
      if (response.code === 200) {
        toastr.success("Reloaded successfully.");
        loadTabContent();
      } else throw new Error(response.message);
    })
    .catch((err) => {
      endLoader();
      toastr.error(err.message);
    });
};

/******************** Programs Section ---- */
const addProgram = () => {
  let btnAddProgram = document.getElementById("btnAddProgram");
  let divAddProgram = document.getElementById("divAddProgram");
  let inputId = document.getElementById("inputId");

  if (divAddProgram.classList.contains("d-none")) {
    inputId.value = "";
    divAddProgram.classList.remove("d-none");
    btnAddProgram.innerText = "Close";
  } else {
    document.getElementById("formProgram").reset();
    divAddProgram.classList.add("d-none");
    btnAddProgram.innerText = "Add Program";
  }
}

const saveProgram = () => {
  let inputId = document.getElementById("inputId");
  let inputName = document.getElementById("inputName");
  let inputRootFolderPath = document.getElementById("inputRootFolderPath");
  let btnSaveProgram = document.getElementById("btnSaveProgram");

  let _id = inputId.value.trim();
  let name = inputName.value.trim();
  if (name == "") {
    toastr.error("name is required");
    inputName.focus();
    return;
  }
  let folderPath = inputRootFolderPath.value.trim();
  if (folderPath == "") {
    toastr.error("Folder path is required");
    inputRootFolderPath.focus();
    return;
  }
  if(!folderPath.endsWith('/')){
    toastr.error("Folder path should end with '/' ");
    inputRootFolderPath.focus();
    return;
  }

  startLoader();
  fetch(_id == "" ? "../program/create" : `../program/update/${_id}`, {
    method: "POST",
    body: JSON.stringify({
      name: name,
      root_folder_path: folderPath,
    }),
    headers: {
      "content-type": "application/x-www-form-urlencoded",
    },
  })
    .then((response) => response.json())
    .then((response) => {
      endLoader();
      if (response.code === 200) {
        loadTabContent();
      } else throw new Error(response.message);
    })
    .catch((err) => {
      endLoader();
      toastr.error(err.message);
    });
};

const editProgram = (id, name, folderPath) => {
  let btnAddProgram = document.getElementById("btnAddProgram");
  let divAddProgram = document.getElementById("divAddProgram");
  let inputId = document.getElementById("inputId");
  let inputName = document.getElementById("inputName");
  let inputRootFolderPath = document.getElementById("inputRootFolderPath");

  if (divAddProgram.classList.contains("d-none")) {
    divAddProgram.classList.remove("d-none");
    btnAddProgram.innerText = "Close";
  } else {
    divAddProgram.classList.add("d-none");
    btnAddProgram.innerText = "Add User";
  }
  inputId.value = id;
  inputName.value = name;
  inputRootFolderPath.value = folderPath;
};

/******************** Programs Section end ---- */
