<?php

namespace App\Controllers;

use GuzzleHttp\Client;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class FunctionController extends BaseController
{
    public bool $api = false;
    public function sendResponse($responseData, $statusCode = 200)
    {
        if($this->api){
            header("Content-type: application/json; charset=utf-8", true, $statusCode);
            echo json_encode($responseData, true, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }else{
            return $responseData;
        }

    }

    public function locale($key)
    {
        if(!isset($_SESSION['user_language'])){
            $_SESSION['user_language'] = 'pt';
        }

        if($content = file_get_contents("../config/locales/locale.json")){
            $locales = json_decode($content, true);

            $value = $locales[$key][$_SESSION['user_language']];
            if ($this->api){
                return $value;
            }
            echo $value;
        }
    }

    public function normalizeString($str): string|null
    {
        $str = strtolower($str);
        $str = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
        $str = preg_replace('/[^a-z0-9\-]+/', '-', $str);
        $str = trim($str, '-');
        $str = preg_replace('/\-+/', '-', $str);

        return $str;
    }

    public function sendMail($to, $title, $body, $url=false, $button=false, $files=false): true|string
    {
        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        if($button){
            $button = "";
        }else{
            $button = "";
        }

        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                              //Send using SMTP
            $mail->Host       = $settings['email_smtp_server'];           //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                     //Enable SMTP authentication
            $mail->Username   = $settings['email_username'];              //SMTP username
            $mail->Password   = $settings['email_password'];              //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;           //Enable implicit TLS encryption
            $mail->Port       = $settings['email_port'];                  //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            $mail->CharSet = 'UTF-8';
            //Recipients
            $mail->setFrom($settings['email_username']);
            //$mail->addAddress('joe@example.net');     //Add a recipient
            $mail->addAddress($to);                     //Name is optional

            //Attachments
            if (!empty($files) && is_array($files)) {
                foreach ($files as $file) {
                    $mail->addAttachment($file['path'], $file['name']);    //Optional name
                }

            }

            $mail->isHTML(true);
            $mail->Subject = $title;
            $mail->Body    = "";

            $mail->send();
            return true;
        } catch (Exception $e) {
            return $mail->ErrorInfo;
        }
    }

    public function sendRequest($requestData, $url, $method)
    {
        $client = new Client();

        $response = $client->$method($url, [
            'headers' => ['Content-Type' => 'application/json'],
            ($method == "get" ? 'query': 'body') => ($method == "get" ? $requestData : json_encode($requestData, true))
        ]);

        try {
            return json_decode($response->getBody());
        }catch (Exception $e){
            return ["error" => $e->getMessage()];
        }
    }

}