<?php
namespace Umb\SystemBackup\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model{

    protected $fillable = ['name', 'root_folder_path', 'created_by'];

}

