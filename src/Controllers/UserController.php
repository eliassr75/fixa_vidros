<?php

namespace App\Controllers;

use App\Models\Anime;
use App\Models\ExplicitGenres;
use App\Models\Genres;
use Exception;
use Statickidz\GoogleTranslate;

class UserController extends BaseController
{
    public function showAll()
    {
        define('TITLE_PAGE', 'Fixa Vidros - Usuários');
        $this->render('users', []);
    }
}