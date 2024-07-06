<?php

namespace App\Controllers;

use App\Models\User;
use Exception;
use Statickidz\GoogleTranslate;

define('TITLE_PAGE', 'Fixa Vidros - Dados Complementares');

class MissingDataController extends BaseController
{

    public function verify($userId): bool
    {
        $userModel = new User();
        $user_search = $userModel->find($userId);

        $requires = [];
        foreach($userModel->missingDataKeys as $key) {
            if (empty($user_search->$key)){
                $requires[] = $key;
            }
        }

        if (!empty($requires)) {
            return false;
        }else{
            return true;
        }
    }
    public function index($userId, $requires=[])
    {

        $userModel = new User();
        $functionController= new FunctionController();
        $user_search = $userModel->find($userId);

        $requires = [];
        foreach($userModel->missingDataKeys as $key) {
            $key_name = $key['name'];
            if (!$user_search->$key_name){
                $requires[] = $key;
            }
        }

        $_SESSION['user_register_missing_data'] = $userId;

        $this->render('missing_data', [
            "login" => true,
            "requires" => $requires,
            "route" => "/missing-data/{$userId}/",
            "missingDataKeys" => $userModel->missingDataKeys,
        ]);
    }
    public function missingData($userId)
    {
        $functionsController= new FunctionController();
        $functionsController->api = true;
        $status_code = 200;

        $response = $functionsController->baseResponse();

        if ($userId === $_SESSION['user_register_missing_data']):
            $data = $functionsController->putStatement();

            $userModel = new User();
            $user_search = $userModel->find($userId);
            $have_update = false;

            foreach ($userModel->missingDataKeys as $key):
                $key_name = $key['name'];

                if (isset($data->$key_name)) :
                    $have_update = true;

                    if ($key_name === 'birthday'):
                        if (!empty($data->$key_name)):
                            $birthday = str_replace('/', '-', $data->$key_name);
                            $birthday = date('Y-m-d', strtotime($birthday));

                            $user_search->$key_name = $birthday;
                            $user_search->age = $functionsController->timeDiff($birthday, date('Y-m-d'), 'years');
                        endif;
                    elseif ($key_name === 'phone_number'):
                        if (strlen($data->$key_name) >= 14):
                            $user_search->$key_name = $data->$key_name;
                        endif;
                    else:
                        $user_search->$key_name = $data->$key_name;
                    endif;
                endif;
            endforeach;

            if ($have_update):
                $user_search->save();
                $user_search->setLog('MissingData', "Usuário atualizou seu cadastro");
                $response->message = $functionsController->locale('register_success_update');
                $response->status = "success";
            else:
                $response->message = $functionsController->locale('redirecting');
                $response->status = "info";
            endif;

            $response->url = "/dashboard/";

        else:
            $response->message = $functionsController->locale('operation_denied_user_register');
            $response->status = "error";
            $response->custom_timer = 5;
            $status_code = 403;
        endif;

        $functionsController->sendResponse($response, $status_code);
    }
}