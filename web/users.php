<?php

use Umb\SystemBackup\Models\User;
use Umb\SystemBackup\Models\System;
use Umb\SystemBackup\Models\Program;

require_once __DIR__ . "/../vendor/autoload.php";
$accessLevel = $_GET['access_level'];
$programId = $_GET['program_id'];
/** @var User[] */
$users = [];
/** @var System[] */
$systems = [];
/** @var Program */
$programs = [];
switch ($accessLevel) {
    case "Admin": {
            $users = User::all();
            $systems = System::all();
            $programs = Program::all();
            break;
        }
    case "Program": {
            if (!$programId) die('Program not provided');
            $users = User::where('program_id', $programId)->where('access_level', 'NOT LIKE', 'Admin')->get();
            $systems = System::where('program_id', $programId)->get();
            $programs = Program::where('id', $programId)->get();
            break;
        }
    default: {
            die('Unable to proceed...');
            break;
        }
}
?>

<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-body">
            <h4>Users</h4>
            <button id="btnAddUser" class="btn btn-secondary float-right" style="margin-top: -50px;" onclick="addUser()">Add User</button>
            <div class="add-user d-none" id="divAddUser">

                <form action="" method="POST" onsubmit="event.preventDefault();" id="formUser">
                    <div class="row">
                        <input type="text" id="inputUserId" hidden>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="inputLastName">Last Name</label>
                                <input type="text" class="form-control" id="inputLastName" required name="last_name" placeholder="Last Name">
                            </div>
                            <div class="form-group">
                                <label for="inputFirstName">First Name</label>
                                <input type="text" class="form-control" id="inputFirstName" required name="first_name" placeholder="First Name">
                            </div>
                            <div class="form-group">
                                <label for="inputMiddleName">Middle Name</label>
                                <input type="text" class="form-control" id="inputMiddleName" name="middle_name" placeholder="Middle Name">
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="inputPhoneNumber">Phone Number</label>
                                <input type="number" class="form-control" id="inputPhoneNumber" name="phone_number" maxlength="10" placeholder="07********" required>
                            </div>
                            <div class="form-group">
                                <label for="inputEmail">Email</label>
                                <input type="text" class="form-control" id="inputEmail" name="email" placeholder="Enter email name" required>
                            </div>
                            <div class="form-group">
                                <label for="">Access Level</label>
                                <select name="access_level" id="selectAccessLevel" class="form-control" onchange="accessLevelChanged()">
                                    <option value="" <?php echo $id == '' ? 'selected' : '' ?> hidden>Select level</option>
                                    <?php if ($accessLevel == 'Admin') : ?><option value="Admin">Administrator</option> <?php endif; ?>
                                    <option value="Program">Program</option>
                                    <option value="Facility">Facility / Systems</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="selectProgram">Program</label>
                                <select name="program_id" id="selectProgram" class="form-control" required>
                                    <option value="" selected hidden>Select Program</option>
                                    <?php foreach ($programs as $program) : ?>
                                        <option value="<?php echo $program->id ?>"><?php echo $program->name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group" id="divSelectSystem">
                                <label for="selectSystemForm">Select System</label>
                                <select class="select2" id="selectSystemForm" name="system_ids[]" multiple="multiple" data-placeholder="Select systems">
                                    <?php
                                    for ($i = 0; $i < sizeof($systems); $i++) :
                                        $system = $systems[$i];
                                    ?>
                                        <option value="<?php echo $system->id; ?>"><?php echo $system->name; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" name="savebtn" id="btnSaveUser" class="btn btn-primary" onclick="saveUser()">Save
                        </button>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-info table-striped" id="tableUsers">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Access Level/Systems</th>
                            <th>Program</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user) : ?>
                            <tr>
                                <td></td>
                                <td><?php echo $user->getNames() ?></td>
                                <td><?php echo $user->email ?></td>
                                <td><?php echo $user->phone_number ?></td>
                                <td>
                                    <?php
                                    if ($user->access_level == 'Program') echo "<span class=\"badge badge-secondary rounded-pill\">Program</span>";
                                    else {
                                        $ids = explode(',', $user->system_ids);
                                        foreach ($systems as $system) {
                                            if (in_array($system->id, $ids)) echo "<span class=\"badge badge-warning m-1 rounded-pill\">$system->name</span>";
                                        }
                                    }
                                    ?>
                                </td>
                                <td><?php echo $user->program()->name ?></td>
                                <td>
                                    <p class="link_edit_user" onclick='editUser(<?php echo json_encode($user) ?>)'>
                                        Edit </p>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .add-user {
        padding: 10px;
        border-bottom: #09ADF1 5px solid;
        border-bottom-left-radius: 5px;
        border-bottom-right-radius: 5px;
        margin-bottom: 10px;
    }

    .link_edit_user {
        color: #009610;
        cursor: pointer;
    }
    .link_edit_user:hover{
        text-decoration: underline;
    }
</style>

<script>
    $(function() {
        $('.select2').select2()
    })
</script>