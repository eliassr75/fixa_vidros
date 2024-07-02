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
use App\Controllers\MenuController;
use App\Controllers\AnimeController;
use App\Controllers\UserController;


$router = new Router();


// ROTAS USERS - GET
$router->addRoute('GET', '/users/', UserController::class, 'showAllUsers');
$router->addRoute('GET', '/users/{userId}/post/{newId}/', UserController::class, 'getUserById');

//ROTAS ANIMES - GET
$router->addRoute('GET', '/animes/', AnimeController::class, 'showAllAnimes');
$router->addRoute('GET', '/anime/create/{malId}/', AnimeController::class, 'createAnime');

$router->handleRequest();
