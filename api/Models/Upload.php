<?php
namespace Umb\SystemBackup\Models;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model{

    protected $fillable = ['facility_id', 'file_name', 'created_by', 'uploaded_to_sharepoint'];

    /**
     * @return Facility;
     */
    public function facility() : Facility{
        return Facility::find($this->facility_id);
    }
}

