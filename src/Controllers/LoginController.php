<?php

namespace App\Controllers;
use App\Models\User;
use App\Validators\Validator;

class LoginController extends BaseController
{
    public function authenticate()
    {

        $functions = new FunctionController();
        $functions->api = true;
        $data = $functions->postStatement($_POST);

        $message = ['message' => $functions->locale('invalid_login'), 'status' => 'error'];

        if(!empty($data->email) && !empty($data->password)){

            if(!Validator::validateEmail($data->email)){
                $functions->sendResponse($message, 404);
            }

            $userModel = new User();
            $user_search = $userModel->where('email', str($data->email))->first();
            if(!empty($user_search)){
                if(!password_verify($data->password, $user_search->password)){
                    $functions->sendResponse($message, 404);
                }
            }else{
                $functions->sendResponse($message, 404);
            }

        }else{
            $functions->sendResponse($message, 404);
        }

//        $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
//        if(!password_verify($password, $data->password)){
//
//        }
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