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
        $user_search = $userModel->where("id", $userId)->first();

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
        $user_search = $userModel->where("id", $userId)->first();

        $requires = [];
        foreach($userModel->missingDataKeys as $key) {
            if (!$user_search->$key){
                $requires[] = $key;
            }
        }

        $this->render('missing_data', [
            "login" => true,
            "requires" => $requires,
            "route" => "/missing-data/{$userId}/",
            "missingDataKeys" => $userModel->missingDataKeys,
        ]);
    }
    public function missingData($userId)
    {

    }
}