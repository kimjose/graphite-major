<?php

namespace Umb\SystemBackup\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model{

    protected $fillable = ['user_id', 'code', 'is_used', 'expires_at'];

    public $timestamps = false;

}
