<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Umb\SystemBackup\Models\Upload;

$systemId = $_GET['system_id'];

$pendingBadge = "<span class=\"badge badge-warning rounded-pill\">Pending</span>";
$completedBadge = "<span class=\"badge badge-success rounded-pill\">Completed</span>";

/**@var Upload[] */
$uploads = Upload::where('system_id', $systemId)->orderBy('created_at', 'desc')->limit(10)->get();
//  print_r($uploads);
?>

<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-body">
            <p>Kindly upload a zipped file to save on space.</p>
            <form action="" method="POST" id="formUploadFile" onsubmit="event.preventDefault()">
                <div class="form-group">
                    <input type="file" name="upload_file" id="inputFile" class="form-control">
                </div>
                <div class="progress-bar mt-2 d-none">
                    <div id="progress" class=""></div>
                </div>
                <input type="submit" value="Upload" class="btn btn-primary mt-3" onclick="uploadFile()">

            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Upload Logs</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-info table-bordered table-stripped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>File Name</th>
                            <th>Uploaded By</th>
                            <th>Uploaded to Sharepoint</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($uploads as $upload): ?>
                            <tr>
                                <td></td>
                                <td><?php echo $upload->file_name ?></td>
                                <td><?php echo $upload->createdBy()->getNames() ?></td>
                                <td><?php echo $upload->uploaded_to_sharepoint == 1 ? $completedBadge : $pendingBadge ?> <p class="text-danger <?php echo ($upload->upload_error != null && $upload->upload_error != '') ? '' : 'd-none' ?>"><?php echo $upload->upload_error ?></p> </td>
                            </tr>
                            <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .progress-bar {
        width: 100%;
        height: 20px;
        background-color: #ddd;
    }

    #progress {
        width: 0;
        height: 100%;
        background-color: #4CAF50;
    }
</style>