<?php

use Umb\SystemBackup\Models\User;
use Umb\SystemBackup\Models\Facility;


require_once __DIR__ . "/../vendor/autoload.php";
$users = User::all();
$facilities = Facility::all();
?>

<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-body">
            <h4>Users</h4>
            <button class="btn btn-secondary float-right" style="margin-top: -50px;" ><i class="fa fa-plus" data-toggle="modal" data-target="#modalUser" ></i> Add User</button>
            <div class="table-responsive">
                <table class="table table-bordered table-info table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Facilities</th>
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
                                <td></td>
                                <td></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- User Dialog -->
<div class="modal fade" id="modalUser" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">User Dialog</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="" method="POST" onsubmit="event.preventDefault();" id="formUser">

                <div class="modal-body">


                    <div class="col-md-6 col-sm-12 form-group">
                        <label for="inputLastName">Last Name</label>
                        <input type="text" class="form-control" id="inputLastName" required name="last_name" placeholder="Last Name">
                    </div>
                    <div class="col-md-6 col-sm-12 form-group">
                        <label for="inputFirstName">First Name</label>
                        <input type="text" class="form-control" id="inputFirstName" required name="first_name" placeholder="First Name">
                    </div>
                    <div class="col-md-6 col-sm-12 form-group">
                        <label for="inputLastName">Last Name</label>
                        <input type="text" class="form-control" id="inputMiddleName" name="middle_name" placeholder="Last Name">
                    </div>
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
                            <option value="Program">Program</option>
                            <option value="Facility">Facility</option>
                        </select>
                    </div>



                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" name="savebtn" id="btnSaveUser" class="btn btn-primary" onclick="saveUser()">Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- User Dialog end-->