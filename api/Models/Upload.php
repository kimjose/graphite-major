<?php
namespace Umb\SystemBackup\Models;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model{

    protected $fillable = ['system_id', 'file_name', 'created_by', 'uploaded_to_sharepoint'];

    /**
     * @return System;
     */
    public function system() : System{
        return System::find($this->system_id);
    }
}

