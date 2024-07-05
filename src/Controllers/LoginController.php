<?php

namespace App\Controllers;
use App\Models\User;
use App\Validators\Validator;
use Cassandra\Exception\ValidationException;
use Exception;

class LoginController extends BaseController
{

    public function redirectToLogin()
    {
        header('Location: /login/');
    }

    public function logout()
    {
        session_destroy();
        $this->redirectToLogin();
    }

    public function authenticate()
    {

        $functions = new FunctionController();
        $functions->api = true;
        $data = $functions->postStatement($_POST);

        $message = ['message' => $functions->locale('invalid_login'), 'status' => 'error'];

        if(!empty($data->email) && !empty($data->password)){

            if(!Validator::validateEmail($data->email)){
                $functions->sendResponse($message, 403);
            }

            if(!isset($_SESSION['access_attempt_counter'])){
                $_SESSION['access_attempt_counter'] = 0;
            }

            $userModel = new User();
            $user_search = $userModel->where('email', str($data->email))->first();
            if(!empty($user_search)){

                $_SESSION['user_language'] = $user_search->language;

                if(!$user_search->permissions()->exists()){

                    $message['message'] = $functions->locale('login_not_permission');
                    $message['status'] = 'warning';
                    $message['dialog'] = true;

                    $functions->sendResponse($message, 403);
                }elseif(!$user_search->active){
                    $functions->sendResponse($message, 403);

                }elseif(!password_verify($data->password, $user_search->password)){

                    $_SESSION['access_attempt_counter']++;
                    $message['message'] = $message['message'] . " <hr class='border'> {$functions->locale('access_attempt_counter')}: " . (4 - $_SESSION['access_attempt_counter']);


                    if((4 - $_SESSION['access_attempt_counter']) === 0){
                        $user_search->active = false;
                        $user_search->save();
                        $user_search->setLog("Login", "Conta bloqueada após 3 tentativas de acesso inválidas.");
                        $_SESSION['access_attempt_counter'] = 0;

                        $message['dialog'] = true;
                        $message['message'] = $functions->locale('locked_account');
                    }

                    $functions->sendResponse($message, 403);

                }else{

                    //$userModel->startSession();

                    $missingDataController = new MissingDataController();
                    if($missingDataController->verify($user_search->id)){
                        $message['url'] = '/dashboard/';
                    }else{
                        $message['url'] = "/missing-data/{$user_search->id}/";
                    }

                    $message['message'] = $functions->locale('welcome') . " - {$user_search->name}";
                    $message['status'] = 'success';
                    $message['custom_timer'] = 2;
                    $message['spinner'] = true;

                    $functions->sendResponse($message);
                }
            }else{
                $functions->sendResponse($message, 404);
            }

        }else{
            $functions->sendResponse($message, 204);
        }

//        $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
//        if(!password_verify($password, $data->password)){
//
//        }
    }

    public function createAccount()
    {
        $functions = new FunctionController();
        $functions->api = true;
        $data = $functions->postStatement($_POST);

        $status_code = 403;
        $message = ['message' => $functions->locale('invalid_login'), 'status' => 'error'];

        try{

            $userModel = new User();
            $userModel->name = $data->name;
            $userModel->email = $data->email;
            $userModel->username = explode('@', $data->email)[0];
            $userModel->password = password_hash($data->password, PASSWORD_BCRYPT);
            if($userModel->validate()){
                $userModel->save();
                $userModel->setLog("Login", "Conta criada.");
                //$userModel->permissions()->attach(1);
            }

            $message['message'] = $functions->locale('valid_user_created') . " - {$data->name} <hr class='border'>" . $functions->locale('wait_administrator_liberation');
            $message['status'] = 'success';
            $message['url'] = '/login/';
            $message['custom_timer'] = 5;
            $message['spinner'] = true;
            $status_code = 200;

        }catch(Exception $e){
            $message['message'] = $e->getMessage();
        }

        $functions->sendResponse($message, $status_code);
    }


    public function forgetPassword()
    {
        define('TITLE_PAGE', 'Fixa Vidros - Recuperação de Senha');
        $this->render('forget_password', ["login" => true, "route" => "/forget-password/"]);
    }

    public function newAccount()
    {
        define('TITLE_PAGE', 'Fixa Vidros - Nova Conta');
        $this->render('new_account', ["login" => true, "newAccount" => true, "route" => "/new-account/"]);
    }

    public function login()
    {
        define('TITLE_PAGE', 'Fixa Vidros - Login');
        $this->render('login', ["login" => true, "route" => "/login/"]);
    }
}