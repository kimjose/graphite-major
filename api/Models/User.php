<?php
namespace Umb\SystemBackup\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model{

    protected $fillable = ['access_level', 'program_id', 'first_name', 'middle_name', 'last_name', 'email', 'phone_number', 'last_login', 'system_ids', 'active', 'created_by'];

    protected $hidden = ['password'];

    public function getNames(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function program(): Program {
        return Program::find($this->program_id);
    }

}
