<?php

namespace Umb\SystemBackup\Controllers;

use Umb\SystemBackup\Models\User;
use Umb\SystemBackup\Controllers\Utils\Utility;
use Umb\SystemBackup\Models\Otp;

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

    public function requestOtp($data){
        try {
            $attributes = ['email'];
            $missing = Utility::checkMissingAttributes($data, $attributes);
            throw_if(sizeof($missing) > 0, new \Exception("Missing parameters passed : " . json_encode($missing)));
            $user = User::where('email', $data['email'])->first();
            if($user == null) throw new \Exception("Error Processing : User not found.", -1);
            $recipient['address'] = $user->email;
            $recipient['name'] = $user->username;
            $recipients[] = $recipient;
            $subject = "System OTP";
            $message = '';
        
            // has otp
            $oldOtp = Otp::where('user_id', $user->id)->where('is_used', 0)->first();
            if($oldOtp){
                $message = "Hello {$user->first_name}, Your OTP for Systems backup is {$oldOtp->code }. The OTP expires at {$oldOtp->expires_at} ";
            } else{
                $d = date("Y-m-d G:i:s");
                $expirely = date("Y-m-d G:i:s", strtotime($d .' + 5 minute'));
                $otp = Otp::create([
                    "user_id" => $user->id,
                    "code" => rand(1000, 9999),
                    "expires_at" => $expirely
                ]);
                $message = "Hello {$user->first_name}, Your OTP for Systems backup is {$oldOtp->code }. The OTP expires at {$oldOtp->expires_at} ";
            }
            Utility::sendMail($recipients, $subject, $message);
            response(SUCCESS_RESPONSE_CODE, "Otp sent");
        } catch (\Throwable $th) {
            Utility::logError(SUCCESS_RESPONSE_CODE, $th->getMessage());
            response(PRECONDITION_FAILED_ERROR_CODE, $th->getMessage());
            http_response_code(PRECONDITION_FAILED_ERROR_CODE);
        }
    }
}
