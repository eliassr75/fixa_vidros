<?php

namespace App\Controllers;

use App\Models\Permissions;
use App\Models\User;
use App\Models\ExplicitGenres;
use Exception;
use Statickidz\GoogleTranslate;

class UserController extends BaseController
{
    public function updateUser($userId)
    {

    }
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

        $this->render('users', [
            'users' => $users_array,
            'permissions' => $permissions,
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
}