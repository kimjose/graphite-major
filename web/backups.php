<?php
require_once __DIR__ . "/../vendor/autoload.php";
$facilityId = $_GET['facility_id'];
$facility = \Umb\SystemBackup\Models\Facility::find($facilityId);

$files = \Umb\SystemBackup\Models\DriveFile::where('folder_path_id', $facility->folder_id)
?>
<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-body">
            <h4>Backups</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-info">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Name</th>
                        <th>Size</th>
                        <th>Download</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
