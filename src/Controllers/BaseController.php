<?php

namespace App\Controllers;

use PDO;

class BaseController
{

    protected function render($view, $data = [])
    {

        if(!in_array('login', $data)) {
            $data['login'] = false;
        }

        extract($data);
        require "../src/Views/{$view}/index.php";
    }
}
