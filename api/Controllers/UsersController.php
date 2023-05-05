<?php

namespace Umb\SystemBackup\Controllers;

use Umb\SystemBackup\Models\User;
use Umb\SystemBackup\Controllers\Utils\Utility;

class UsersController
{

    public function createUser($data)
    {
        try {
            $attributes = ['access_level', 'first_name', 'middle_name', 'last_name', 'email', 'phone_number', 'facility_ids'];
            $missing = Utility::checkMissingAttributes($data, $attributes);
            throw_if(sizeof($missing) > 0, new \Exception("Missing parameters passed : " . json_encode($missing)));
            $user = User::create($data);
            response(SUCCESS_RESPONSE_CODE, "User created successfully.", $user);
        } catch (\Throwable $th) {
            Utility::logError(SUCCESS_RESPONSE_CODE, $th->getMessage());
            response(PRECONDITION_FAILED_ERROR_CODE, $th->getMessage());
            http_response_code(PRECONDITION_FAILED_ERROR_CODE);
        }
    }

    public function updateUser($id, $data)
    {
        try {
            $attributes = ['access_level', 'first_name', 'middle_name', 'last_name', 'email', 'phone_number', 'facility_ids'];
            $missing = Utility::checkMissingAttributes($data, $attributes);
            throw_if(sizeof($missing) > 0, new \Exception("Missing parameters passed : " . json_encode($missing)));
            $user = User::findOrFail($id);
            $user->update($data);
            response(SUCCESS_RESPONSE_CODE, "User created successfully.", $user);
        } catch (\Throwable $th) {
            Utility::logError(SUCCESS_RESPONSE_CODE, $th->getMessage());
            response(PRECONDITION_FAILED_ERROR_CODE, $th->getMessage());
            http_response_code(PRECONDITION_FAILED_ERROR_CODE);
        }
    }
}
