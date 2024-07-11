<?php

namespace App\Controllers;
require_once '../config/cors.php';
require '../vendor/autoload.php';
require '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === "GET"){
    header("Content-type: text/html; charset=utf-8");
}else{
    header("Content-type: application/json; charset=utf-8");
}

use App\Middleware;
use App\Router;

$middleware = new Middleware();
$router = new Router();

//MAIN ROUTES
$router->addRoute('GET', '', false,  "login",LoginController::class, 'redirectToLogin');
$router->addRoute('GET', '/', false,  "login",LoginController::class, 'redirectToLogin');
$router->addRoute('GET', '/error/{codeError}/', false,  "",ErrorController::class, 'errorPage');

// LOGIN ROUTES
$router->addRoute('GET', '/logout/', false,  "login",LoginController::class, 'logout');

$router->addRoute('GET', '/login/', false,  "login",LoginController::class, 'login');
$router->addRoute('POST', '/login/', false,  "login",LoginController::class, 'authenticate');

$router->addRoute('GET', '/new-account/', false,  "login",LoginController::class, 'newAccount');
$router->addRoute('POST', '/new-account/', false,  "login",LoginController::class, 'createAccount');

$router->addRoute('GET', '/forget-password/', false,  "login",LoginController::class, 'forgetPassword');
$router->addRoute('POST', '/forget-password/', false,  "login",LoginController::class, 'forgetPassword');

$router->addRoute('GET', '/forget-password/{token}/', false,  "login",LoginController::class, 'forgetPassword');
$router->addRoute('PUT', '/forget-password/{token}/', false,  "login",LoginController::class, 'resetPassword');

$router->addRoute('GET', '/missing-data/{userId}/', true,  "system",MissingDataController::class, 'index');
$router->addRoute('PUT', '/missing-data/{userId}/', true, "system",MissingDataController::class, 'missingData');

// SYSTEM ROUTES
$router->addRoute('GET', '/dashboard/', true, "system",DashboardController::class, 'index');

$router->addRoute('GET', '/users/', true, "system",UserController::class, 'index');
$router->addRoute('PUT', '/users/{userId}/', true, "system",UserController::class, 'updateUser');
$router->addRoute('GET', '/users/json/', true, "system",UserController::class, 'json');
$router->addRoute('GET', '/users/json/{userId}/', true, "system",UserController::class, 'json');

$middleware->autoRedirect();
$router->handleRequest();
