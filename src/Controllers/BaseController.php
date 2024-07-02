<?php

namespace App\Controllers;

use PDO;

class BaseController
{
    public $functions;

    protected function render($view, $data = [])
    {
        extract($data);
        require "../src/Views/{$view}/index.php";
    }
}
