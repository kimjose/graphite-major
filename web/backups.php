<?php

use Umb\SystemBackup\Models\DriveFile;

require_once __DIR__ . "/../vendor/autoload.php";
$systemId = $_GET['system_id'];
$system = \Umb\SystemBackup\Models\System::find($systemId);
// $files = DriveFile::all();
$files = \Umb\SystemBackup\Models\DriveFile::where('folder_id', $system->folder_id)->get();
$c = 1;
?>
<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-body">
            <h4>Backups</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-info">
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
                                    <a href="<?php echo $file->download_url ?>" target="_blank"> <i></i> Download</a>
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