<?php

use Umb\SystemBackup\Models\Program;
use Umb\SystemBackup\Models\User;
use Umb\SystemBackup\Models\System;

$accessLevel = $_GET['access_level'];
$programId = $_GET['program_id'];
require_once __DIR__ . "/../vendor/autoload.php";
/** @var User[] */
$users = [];
/** @var System[] */
$systems = [];
/** @var Program */
$programs = [];
switch ($accessLevel) {
    case "Admin": {
            $systems = System::all();
            $programs = Program::all();
            break;
        }
    case "Program": {
            if (!$programId) die('Program not provided');
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
            <h4>Systems</h4>
            <button id="btnAddSystem" class="btn btn-secondary float-right" style="margin-top: -50px;" onclick="addSystem()">Add System</button>
            <div class="d-none" id="divAddSystem">

                <form action="" method="POST" onsubmit="event.preventDefault();" id="formSystem">
                    <input type="text" id="inputId" value="" hidden>

                    <div class="form-group">
                        <label for="selectProgram">Program</label>
                        <select name="program_id" id="selectProgram" class="form-control" required>
                            <option value="" selected hidden>Select Program</option>
                            <?php foreach ($programs as $program) : ?>
                                <option value="<?php echo $program->id ?>"><?php echo $program->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="inputName">System Name</label>
                        <input type="text" class="form-control" id="inputName" required name="name" placeholder="System Name">
                    </div>
                    <div class="form-group">
                        <label for="inputFolderId">Folder Id</label>
                        <div>
                            <div class="float-right btn" style="margin-top: -35px; text-align: right; color:#09ADF1" onclick="toggleGetFolderId()">Get Folder ID</div>
                            <div id="divGetFolderId" class="d-none">
                                <input type="text" class="form-control" id="inputFolderPath" required name="path" placeholder="Folder Path">
                                <buttton class="btn btn-outline-secondary mt-2" onclick="getFolderId()">Get Id</buttton>
                                <buttton class="btn btn-outline-info mt-2 d-none" id="btnCreateFolder" onclick="createFolder()">Create Folder</buttton>
                            </div>
                        </div>
                        <input type="text" class="form-control" id="inputFolderId" required name="folder_id" placeholder="Folder Id">
                    </div>
                    <button type="submit" name="savebtn" id="btnSaveSystem" class="btn btn-primary" onclick="saveSystem()">Save
                    </button>

                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-info table-striped" id="tableSystems">
                    <thead>
                        <tr>
                            <th>_ID</th>
                            <th>Name</th>
                            <th>Program</th>
                            <th>Folder Id</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($systems as $system) : ?>
                            <tr>
                                <td><?php echo $system->id ?></td>
                                <td><?php echo $system->name ?></td>
                                <td><?php echo $system->program()->name ?></td>
                                <td><?php echo $system->folder_id ?></td>
                                <td>
                                    <p class="link_edit_system" onclick='editSystem(<?php echo $system->id ?>, <?php echo $system->program_id ?>, "<?php echo $system->name ?>", "<?php echo $system->folder_id ?>")'>
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
    #divAddSystem {
        padding: 10px;
        border-bottom: #09ADF1 5px solid;
        border-bottom-left-radius: 5px;
        border-bottom-right-radius: 5px;
        margin-bottom: 10px;
    }

    .link_edit_system {
        color: #009610;
        cursor: pointer;
    }
    .link_edit_system:hover{
        text-decoration: underline;
    }

    #divGetFolderId {
        margin: 8px;
        border: #000054 dotted 1px;
        padding: 5px;
    }
</style>