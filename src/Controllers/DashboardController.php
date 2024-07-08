<?php

namespace App\Controllers;

use App\Models\User;
use Exception;
use Statickidz\GoogleTranslate;

define('TITLE_PAGE', 'Fixa Vidros - Dados Complementares');

class DashboardController extends BaseController
{
    public function errorPage($userId)
    {
        $this->render('dashboard', []);
    }
}