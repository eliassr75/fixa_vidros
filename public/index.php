<?php

require_once '../config/cors.php';
require '../vendor/autoload.php';
require '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === "GET"){
    header("Content-type: text/html; charset=utf-8");
}else{
    header("Content-type: application/json; charset=utf-8");
}

use App\Router;
use App\Controllers\LoginController;
use App\Controllers\AnimeController;
use App\Controllers\UserController;

$router = new Router();


//MAIN ROUTE
$router->addRoute('GET', '', LoginController::class, 'redirectToLogin');
$router->addRoute('GET', '/', LoginController::class, 'redirectToLogin');

// LOGIN ROUTES
$router->addRoute('GET', '/login/', LoginController::class, 'login');
$router->addRoute('GET', '/new-account/', LoginController::class, 'newAccount');
$router->addRoute('GET', '/forget-password/', LoginController::class, 'forgetPassword');
$router->addRoute('GET', '/logout/', LoginController::class, 'logout');
$router->addRoute('POST', '/login/', LoginController::class, 'authenticate');
$router->addRoute('POST', '/new-account/', LoginController::class, 'createAccount');


$router->addRoute('GET', '/users/', UserController::class, 'showAll');
$router->addRoute('GET', '/users/{userId}/post/{newId}/', UserController::class, 'getUserById');

//ROTAS ANIMES - GET
$router->addRoute('GET', '/animes/', AnimeController::class, 'showAllAnimes');
$router->addRoute('GET', '/anime/create/{malId}/', AnimeController::class, 'createAnime');

$router->handleRequest();
