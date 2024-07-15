<?php

namespace App\Controllers;

use App\Models\Permissions;
use App\Models\User;
use App\Models\ExplicitGenres;
use Exception;
use Statickidz\GoogleTranslate;

class ClientController extends BaseController
{
    public function index()
    {
        $functionController = new FunctionController();
        $functionController->is_dashboard(false);

        define('TITLE_PAGE', 'Fixa Vidros - Usuários');
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_users'));

        $users = User::with('permissions')->orderBy('id', 'desc')->get();
        $users_array = [];
        foreach ($users as $user) {
            $user->str_created = date('d/m/Y H:i', strtotime($user->created_at));
            $user->current_permission = $user->current_permission();
            $users_array[] = $user;
        }

        $permissions = Permissions::orderBy('id', 'asc')->get();

        $this->render('clients', [
            'users' => $users_array,
            'permissions' => $permissions,
            'button'=> 'add'
        ]);
    }

    public function json($userId=false)
    {
        $functionController = new FunctionController();
        $functionController->api = true;

        $user = false;
        $users_array = [];

        if($userId):
            $user = User::find($userId);
            $user->str_created = date('d/m/Y H:i', strtotime($user->created_at));
            $user->current_permission = $user->current_permission();

            $newMissingDataKeys = [];
            foreach ($user->missingDataKeys as $missingDataKey) {
                $key_name = $missingDataKey['name'];
                $missingDataKey['label'] = ucfirst($functionController->locale("input_{$missingDataKey['name']}"));
                if ($key_name === "birthday"):
                    $missingDataKey['value'] = $user->$key_name ? date('d/m/Y', strtotime($user->$key_name)) : null;
                else:
                    $missingDataKey['value'] = $user->$key_name;
                endif;
                $newMissingDataKeys[] = $missingDataKey;
            }
            $user->missing_data = $newMissingDataKeys;

        else:
            $users = User::with('permissions')->orderBy('id', 'desc')->get();
            foreach ($users as $user_obj) {
                $user_obj->str_created = date('d/m/Y H:i', strtotime($user_obj->created_at));
                $user_obj->str_updated = date('d/m/Y H:i', strtotime($user_obj->updated_at));
                $user_obj->str_birthday = date('d/m/Y', strtotime($user_obj->birthday));
                $user_obj->current_permission = $user_obj->current_permission();

                $newMissingDataKeys = [];
                foreach ($user_obj->missingDataKeys as $missingDataKey) {
                    $key_name = $missingDataKey['name'];
                    $missingDataKey['label'] = $functionController->locale("input_{$missingDataKey['name']}");
                    $missingDataKey['value'] = $user_obj->$key_name;
                    $newMissingDataKeys[] = $missingDataKey;
                }
                $user_obj->missing_data = $newMissingDataKeys;

                $users_array[] = $user_obj;
            }
        endif;

        $permissions = Permissions::orderBy('id', 'asc')->get();
        $functionController->sendResponse([
            'users' => $users_array,
            'user' => $user,
            'permissions' => $permissions,
        ]);
    }

    public function updateUser($userId)
    {
        $functionsController = new FunctionController();
        $functionsController->api = true;
        $response = $functionsController->baseResponse();
        $status_code = 200;

        $data = $functionsController->putStatement();
        $userModel = new User();
        $user_search = $userModel->find($userId);
        $have_update = false;
        $invalid_cpf = false;

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
                elseif($key_name === 'cpf'):
                    if(!$functionsController->validaCPF($data->$key_name)):
                        $invalid_cpf = true;
                        $have_update = false;
                        break;
                    else:
                        $user_search->$key_name = $data->$key_name;
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

        $user_search->name = $data->name;
        $user_search->email = $data->email;
        $user_search->username = explode('@', $data->email)[0];
        $user_search->language = $data->language;

        if($user_search->permissions()->exists()):
            $user_search->permissions()->detach();
        endif;
        $user_search->permissions()->attach($data->permission);


        if ($have_update and $user_search->validate()):
            $user_search->save();
            $user_search->setLog('User', "Usuário atualizou seu cadastro");
            $user_search->startSession();

            $response->message = $functionsController->locale('register_success_update');
            $response->status = "success";
            $response->reload = true;

        elseif ($invalid_cpf):
            $response->message = $functionsController->locale('invalid_cpf');
            $response->status = "warning";
            $status_code = 400;

        else:
            $response->message = $functionsController->locale('redirecting');
            $response->status = "info";

        endif;

        $functionsController->sendResponse($response, $status_code);
    }

    public function changeUser($userId)
    {
        $functionsController = new FunctionController();
        $functionsController->api = true;
        $response = $functionsController->baseResponse();
        $status_code = 200;

        $userModel = new User();
        $user_search = $userModel->find($userId);

        $user_search->active = !$user_search->active;
        $user_search->setLog('User', "O status do usuário foi modificado para " . ($user_search->active ? "Ativo" : "Inativo") . " - {$_SESSION['name']}");
        $user_search->save();

        $response->message = $functionsController->locale('register_success_update');
        $response->status = "success";

        if ($userId === $_SESSION['id']):
            $response->reload = true;
            $user_search->startSession();
        endif;
        $functionsController->sendResponse($response, $status_code);
    }
}