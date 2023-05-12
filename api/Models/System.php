<?php

namespace Umb\SystemBackup\Models;

use Illuminate\Database\Eloquent\Model;

class System extends Model {

    protected $table = 'systems';

    protected $fillable = ['name', 'folder_id'];

}
