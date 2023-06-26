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
            $system_ids = [];
            $attributes = ['access_level', 'program_id', 'first_name', 'middle_name', 'last_name', 'email', 'phone_number'];
            $missing = Utility::checkMissingAttributes($data, $attributes);
            throw_if(sizeof($missing) > 0, new \Exception("Missing parameters passed : " . json_encode($missing)));
            extract($data);
            $exists = User::where('email', $email)->first();
            if($exists) throw new \Exception("User already exists...");
            $ids = implode(',', $system_ids);
            $data['system_ids'] = $ids;
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
            $system_ids = [];
            $attributes = ['access_level', 'program_id', 'first_name', 'middle_name', 'last_name', 'email', 'phone_number'];
            $missing = Utility::checkMissingAttributes($data, $attributes);
            throw_if(sizeof($missing) > 0, new \Exception("Missing parameters passed : " . json_encode($missing)));
            extract($data);
            $user = User::findOrFail($id);
            $ids = implode(',', $system_ids);
            $data['system_ids'] = $ids;
            $user->update($data);
            response(SUCCESS_RESPONSE_CODE, "User updated successfully.", $user);
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
            $oldOtp = Otp::where('user_id', $user->id)->where('is_used', 0)->where('expires_at', '>', date('Y-m-d H:i:s'))->first();
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
                $message = "Hello {$user->first_name}, Your OTP for Systems backup is {$otp->code }. The OTP expires at {$otp->expires_at} ";
            }
            $res = Utility::sendMail($recipients, $subject, $message);
            if($res)response(SUCCESS_RESPONSE_CODE, "Otp sent");
            else throw new \Exception('Unable to send mail.', 1);
            
        } catch (\Throwable $th) {
            Utility::logError(SUCCESS_RESPONSE_CODE, $th->getMessage());
            response(PRECONDITION_FAILED_ERROR_CODE, $th->getMessage());
            http_response_code(PRECONDITION_FAILED_ERROR_CODE);
        }
    }

    public function verifyOtp($data): void
    {
        try{
            $attributes = ['email', 'code'];
            $missing = Utility::checkMissingAttributes($data, $attributes);
            throw_if(sizeof($missing) > 0, new \Exception("Missing parameters passed : " . json_encode($missing)));
            $user = User::where('email', $data['email'])->first();
            if($user == null) throw new \Exception("Error Processing : User not found.", -1);
            $otp = Otp::where('user_id', $user->id)->where('code', $data['code'])->where('is_used', 0)->where('expires_at', '>', date('Y-m-d H:i:s'))->first();
            if($otp == null) throw new \Exception("Unable to verify. Try again later", 1);
            $user->last_login = date("Y:m:d h:i:s", time());
			$user->save();
            $otp->update(['is_used' => 1]);
			session_start();
			$sessionData = [];
			$sessionData['user'] = $user;
			$sessionData['expires_at'] = time() + ($_ENV['SESSION_DURATION'] * 60);
			$_SESSION[$_ENV['SESSION_APP_NAME']] = $sessionData;
            response(SUCCESS_RESPONSE_CODE, "Otp verified.");
        } catch(\Throwable $th){
            Utility::logError(SUCCESS_RESPONSE_CODE, $th->getMessage());
            response(PRECONDITION_FAILED_ERROR_CODE, $th->getMessage());
            http_response_code(PRECONDITION_FAILED_ERROR_CODE);
        }
    }

}
