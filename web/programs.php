<?php 

use Umb\SystemBackup\Models\Program;
require_once __DIR__ . "/../vendor/autoload.php";
$programs = Program::all();
?>

<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-body">
            <h4>Programs</h4>
            <button id="btnAddProgram" class="btn btn-secondary float-right" style="margin-top: -50px;" onclick="addProgram()">Add Program</button>
            <div class="d-none" id="divAddProgram">

                <form action="" method="POST" onsubmit="event.preventDefault();" id="formProgram">
                    <input type="text" id="inputId" value="" hidden>
                    <div class="form-group">
                        <label for="inputName">Program Name</label>
                        <input type="text" class="form-control" id="inputName" required name="name" placeholder="Program Name">
                    </div>
                    <div class="form-group">
                        <label for="inputRootFolderPath">Folder path</label>
                        <input type="text" class="form-control" id="inputRootFolderPath" required name="root_folder_path" placeholder="Folder Path">
                    </div>

                    <button type="submit" name="savebtn" id="btnSaveProgram" class="btn btn-primary" onclick="saveProgram()">Save
                    </button>

                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-info table-striped" id="tablePrograms">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Folder Path</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($programs as $program) : ?>
                            <tr>
                                <td><?php echo $program->id ?></td>
                                <td><?php echo $program->name ?></td>
                                <td><?php echo $program->root_folder_path ?></td>
                                <td>
                                    <p class="" id="link_edit_program" onclick='editProgram(<?php echo $program->id ?>, "<?php echo $program->name ?>", "<?php echo $program->root_folder_path ?>")'>
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
    #divAddProgram {
        padding: 10px;
        border-bottom: #09ADF1 5px solid;
        border-bottom-left-radius: 5px;
        border-bottom-right-radius: 5px;
        margin-bottom: 10px;
    }

    #link_edit_program {
        color: #009610;
        cursor: pointer;
    }

</style>