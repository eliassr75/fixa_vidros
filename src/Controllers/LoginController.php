<?php

namespace App\Controllers;
use App\Models\User;

class LoginController extends BaseController
{
    public function authenticate()
    {

        $functions = new FunctionController();
        $functions->api = true;
        $data = $_POST;

        if(!empty($data['email']) && !empty($data['password'])){

            $userModel = new User();
            $user_search = $userModel->where('email', str($data['email']))->first();
            if(!empty($user_search)){
                if(!password_verify($data['password'], $user_search->password)){
                    $functions->sendResponse(['message' => $functions->locale('invalid_login'), 'status' => 'error'], 404);
                }
            }else{
                $functions->sendResponse(['message' => $functions->locale('invalid_login'), 'status' => 'error'], 404);
            }

        }else{
            $functions->sendResponse(['message' => $functions->locale('invalid_login'), 'status' => 'error'], 404);
        }

//        $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
//        if(!password_verify($password, $data['password'])){
//
//        }
    }

    public function newAccount()
    {
        define('TITLE_PAGE', 'Fixa Vidros - Nova Conta');
        $this->render('new-account', ["login" => true, "newAccount" => true, "route" => "/new-account/"]);
    }

    public function login()
    {
        define('TITLE_PAGE', 'Fixa Vidros - Login');
        $this->render('login', ["login" => true, "route" => "/login/"]);
    }
}