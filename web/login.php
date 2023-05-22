<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Sharepoint Backups | Login</title>
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
<style>
    body {
        width: 100%;
        height: calc(100%);
        position: fixed;
        top: 0;
        left: 0
            /*background: #007bff;*/
    }

    main#main {
        width: 100%;
        height: calc(100%);
        display: flex;
    }
</style>

<body class="" style="background: #000020 url('img/cloud-storage.png') no-repeat fixed center;">


    <main id="main">

        <div class="align-self-center w-100">
            <div id="login-center" class=" row justify-content-center m-2">
                <h4 class="text-white text-center"><b><?php echo $_ENV["APP_NAME"] ?? 'System Backup' ?></b></h4>
                <div id="divSendOtp" class="card col-md-6 col-sm-12">
                    <div class="card-body m-2">
                        <div class="col-12 mb-3">
                            <h5 class="text-center">Welcome. Enter your email to continue.</h5>
                        </div>
                        <form id="login-form" action="" method="POST" onsubmit="event.preventDefault()">
                            <div class="form-group mb-2">
                                <label for="inputEmail" class="control-label text-dark">Email</label>
                                <input type="email" id="inputEmail" name="email" class="form-control form-control-sm" value="" required>
                            </div>
                            <div style="text-align: center;"><input class="btn-sm btn-block btn-wave col-md-4 btn-primary" name="submit" type="submit" id="btnRequestOtp" value="Request Otp"></div>
                        </form>
                        <p class="text-sm-center mt-2">Already have OTP click <span class="text-primary" style="cursor: pointer" onclick=""> here </span> to verify.</p>
                    </div>
                </div>

                <div id="divVerifyOtp" class="card col-md-6 col-sm-12 d-none">
                    <div class="card-body m-2">
                        <div class="col-12 mb-3">
                            <h5 id="titleMailSent" class="text-center">An OTP has been sent to email {mail}. enter it below
                                to continue </h5>
                            <p></p>
                        </div>
                        <form id="formVerifyOtp" action="" method="POST" onsubmit="event.preventDefault()">
                            <div class="form-group mb-2">
                                <label for="inputOtp" class="control-label text-dark">OTP</label>
                                <input type="number" maxlength="4" id="inputOtp" name="code" class="form-control form-control-sm" value="" required>
                            </div>
                            <div style="text-align: center;">
                                <button id="buttonVerify" class="btn-sm btn-block btn-wave col-md-4 btn-primary">Verify
                                    OTP
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>


    </main>

    <a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>

    <style>
        #contentSection {
            width: 100%;
        }


        .loader {
            z-index: 5;
            margin-top: -50px;
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
            float: left;
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
        const divSendOtp = document.getElementById('divSendOtp')
        const divVerifyOtp = document.getElementById('divVerifyOtp')
        const inputEmail = document.getElementById('inputEmail')
        const inputOtp = document.getElementById('inputOtp')
        const titleMailSent = document.getElementById('titleMailSent')
        const btnRequestOtp = document.getElementById('btnRequestOtp')
        const buttonVerify = document.getElementById('buttonVerify')

        const goToVerify = () => {}

        btnRequestOtp.addEventListener('click', () => {
            let email = inputEmail.value.trim();
            if (email.length < 6) {
                toastr.error('|Enter a valid email');
                return
            }
            toastr.info("Sending OTP")
            btnRequestOtp.setAttribute('disabled', '')
            fetch('../user/request-otp', {
                    method: 'POST',
                    body: JSON.stringify({
                        email: email
                    }),
                    headers: {
                        "content-type": "application/x-www-form-urlencoded"
                    }
                })
                .then(response => response.json())
                .then(response => {
                    if (response.code === 200) {
                        toastr.success("OTP sent successfully")
                        setTimeout(() => {
                            divSendOtp.classList.add('d-none')
                            if (divVerifyOtp.classList.contains('d-none')) divVerifyOtp.classList.remove('d-none')
                            titleMailSent.innerText = `An OTP has been sent to email ${email}. Enter it below to continue. Check spams if it is not in the default inbox.`
                        }, 997)
                    } else throw new Error(response.message)
                })
                .catch(err => {
                    if (btnRequestOtp.hasAttribute('disabled')) btnRequestOtp.removeAttribute('disabled')
                    toastr.error(err.message)
                })
        })
        buttonVerify.addEventListener('click', () => {
            let otp = inputOtp.value.trim()
            if (otp.length < 4) {
                toastr.error('Enter a valid OTP. Must be 4 digits')
                return
            }
            fetch('../user/verify-otp', {
                    method: 'POST',
                    body: JSON.stringify({
                        code: otp,
                        email: inputEmail.value.trim()
                    }),
                    headers: {
                        "content-type": "application/x-www-form-urlencoded"
                    }
                })
                .then(response => {
                    if (response.ok) {
                        location.replace("index")
                    } else throw new Error(response.statusText)
                })
                .catch(err => {
                    console.log(err.message);
                    toastr.error("Unable to verify OTP")
                })
        })
    </script>

</body>

</html>