<?php
namespace Umb\SystemBackup\Models;

use Illuminate\Database\Eloquent\Model;

class DriveFile extends Model{

    protected $fillable = ['id', 'folder_id', 'name', 'web_url', 'download_url', 'size', 'created_date_time'];

    public $timestamps = false;

}

