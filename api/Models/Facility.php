<?php

namespace Umb\SystemBackup\Models;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model {

    protected $table = 'facilities';

    protected $fillable = ['mfl_code', 'name', 'folder_id'];

}
