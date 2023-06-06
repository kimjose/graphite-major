<?php

namespace Umb\SystemBackup\Controllers\Utils;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Utility
{

    /***
     * Checks for missing attributes
     * @param array $data
     * @param array $attributes
     *
     * @return array - an array of missing attrs
     */
    public static function checkMissingAttributes(array $data, array $attributes): array
    {
        $missingAttrs = [];
        foreach ($attributes as $attribute) {
            if (!isset($data[$attribute])) $missingAttrs[] = $attribute;
        }
        return $missingAttrs;
    }

    public static function logError($code, $message)
    {
        if (!is_dir($_ENV['LOGS_DIR'])) {
            mkdir($_ENV['LOGS_DIR']);
        }
        $today = date_format(date_create(), 'Ymd');
        $handle = fopen($_ENV['LOGS_DIR'] . "errors_" . $today . ".txt", 'a');
        $data = date("Y-m-d H:i:s ", time());
        $data .= "      Code " . $code;
        $data .= "      Message " . $message;
        $data .= "      ClientAddr " . $_SERVER["REMOTE_ADDR"];
        $data .= "\n";
        fwrite($handle, $data);
        fclose($handle);
    }

    public static function uploadFile($newName = '', $dir = null)
    {
        try {
            if (!is_dir($_ENV['PUBLIC_DIR'])) {
                mkdir($_ENV['PUBLIC_DIR']);
            }
            $uploadDir = $dir ?? $_ENV['PUBLIC_DIR'];
            // $uploadedFiles = '';
            $file_name = $_FILES['upload_file']['name'];
            $ext = substr($file_name, strrpos($file_name, '.'));
            $tmp_name = $_FILES['upload_file']['tmp_name'];
            $file_name = str_replace(" ", "_", $file_name);
            $file_name = str_replace("/", "_", $file_name);
            // $file_name = str_replace(".", "_" . time() . ".", $file_name);
            $mF = ($newName == '' ? $file_name : $newName . $ext);
            $uploaded = move_uploaded_file($tmp_name, $uploadDir . $mF);
            if (!$uploaded) throw new \Exception("File not uploaded");
            /*
            foreach ($_FILES['upload_files']['name'] as $file_name) {
                $tmp_name = $_FILES['upload_files']['tmp_name'][$count];
                $file_name = str_replace(" ", "_", $file_name);
                $file_name = str_replace(".", "_" . time() . ".", $file_name);
                $uploaded = move_uploaded_file($tmp_name, $_ENV['PUBLIC_DIR'] . $file_name);
                if (!$uploaded) throw new \Exception("File not uploaded");
                if ($count == (sizeof($_FILES['upload_files']['tmp_name']) - 1)) {
                    $uploadedFiles .= $file_name;
                } else {
                    $uploadedFiles .= $file_name . ',';
                }
                $count++;
            }*/
            return $mF;
        } catch (\Throwable $th) {
            self::logError($th->getCode(), $th->getMessage());
            //            http_response_code(PRECONDITION_FAILED_ERROR_CODE);
            return null;
        }
    }

    /**
     * @param array $recipients an array containing the address and name for recipients of this email [['address', 'name'], []...]
     * @param string $subject The subject message
     * @param string $body The body oof the email. Supports html
     * @param array $attachments An array of attachments  [['path', 'name'], [...]...]. This field is not required.
     * @return void
     */
    public static function sendMail(array $recipients, string $subject, string $body, array $attachments = [])
    {
        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);
        $footer = "<hr> <h4>Click <a href='https://psms.mgickenya.org/system-backup/'>here</a> to open the application. </h4>";
        $body .= $footer;
        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host = $_ENV['MAILER_HOST'];                     //Set the SMTP server to send through
            $mail->SMTPAuth = true;                              //Enable SMTP authentication
            $mail->Username = $_ENV['MAILER_ADDRESS'];                     //SMTP username
            $mail->Password = $_ENV['MAILER_PASSWORD'];                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
            $mail->Port = $_ENV['MAILER_PORT'];                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom($_ENV['MAILER_ADDRESS'], $_ENV['MAILER_NAME']);
            foreach ($recipients as $recipient) {
                $mail->addAddress($recipient['address'], $recipient['name']);
            }

            // $mail->addAddress('ellen@example.com');               //Name is optional
            $mail->addReplyTo('noreply@example.com', 'No Reply');
            //            $mail->addCC('cc@example.com');
            //            $mail->addBCC('bcc@example.com');

            //Attachments
            foreach ($attachments as $attachment) {
                $mail->addAttachment($attachment['path'], $attachment['name'] ?? '');
            }

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $body;
            //            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            return $mail->send();
//            echo 'Message has been sent';
        } catch (\Exception $e) {
            return false;
            // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            self::logError($e->getCode(), $e->getMessage());
        }
    }

    /***
     * This function takes an integer number $num and generates a string similar to columns in a spreadsheet.
     * ie A, B, ...... AA, AB, ..... BA, and so on.
     * given a number 0 = A, 1 = B and so on...
     * @param int $num
     *
     *
     * @return String corresponding column string
     */
    public static function getColumnLabel(int $num): string
    {
        $numeric = $num % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval($num / 26);
        if ($num2 > 0) {
            return self::getColumnLabel($num2 - 1) . $letter;
        } else {
            return $letter;
        }
    }

    /*******
     * This function calculates the distance between two points A and B given the gps coordinates of the points
     * @param $pointA array of [lat, lon]
     * @param $pointB array of [lat, lon]
     *
     * @return double The distance.
     * */
    public static function getDistanceFromCoordinates(array $pointA, array $pointB)
    {
        $radius = 6378; // Radius of the earth in km
        $dLat = self::deg2rad($pointB[0] - $pointA[0]);
        $dLon = self::deg2rad($pointB[1] - $pointA[1]);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(self::deg2rad($pointA[0])) * cos(self::deg2rad($pointB[0])) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $radius * $c;
    }

    public static function deg2rad($deg)
    {
        return $deg * (pi() / 180);
    }

}
