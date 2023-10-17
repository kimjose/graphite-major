<?php

use Umb\SystemBackup\Models\DriveFile;

require_once __DIR__ . "/../vendor/autoload.php";
$systemId = $_GET['system_id'];
$system = \Umb\SystemBackup\Models\System::find($systemId);
// $files = DriveFile::all();
/** DriveFile[] */
$files = \Umb\SystemBackup\Models\DriveFile::where('folder_id', $system->folder_id)->get();
$c = 1;
?>
<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-auto">
                    <h4>Backups</h4>
                </div>
                <div class="col-auto mt-1">
                    <p style="cursor: pointer; color: #00669A" onclick="reloadBackups()"> <i></i> Reload</p>
                </div>
            </div>
            <div class="table-responsive">
                <table id="tableBackups" class="table table-bordered table-info  table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Created At</th>
                            <th>Size</th>
                            <th>Download</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($files as $file) : ?>
                            <tr>
                                <td><?php echo $c ?></td>
                                <td><?php echo $file->name ?></td>
                                <td><?php echo $file->created_date_time ?></td>
                                <td><?php echo number_format((($file->size) / (1024 * 1000)), 2)  . ' MB'  ?></td>
                                <td>
                                    <div class="row">
                                        <div class="col-auto">
                                            <a href="<?php echo $file->download_url ?>" target="_blank"> <i></i> Download</a>
                                        </div>
                                        <div class="col-auto">
                                            <p class="delete-file" onclick="deleteFile('<?php echo $file->id  ?>', '<?php echo $file->folder_id ?>')">Delete</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php
                            $c++;
                        endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .delete-file {
        color: red;
        cursor: pointer;
    }
    .delete-file:hover, a:hover{
        text-decoration: underline;
    }
</style>