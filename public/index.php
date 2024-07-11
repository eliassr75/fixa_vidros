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
$router->addRoute('GET', '', false, LoginController::class, 'redirectToLogin');
$router->addRoute('GET', '/', false, LoginController::class, 'redirectToLogin');
$router->addRoute('GET', '/error/{codeError}/', false, ErrorController::class, 'errorPage');

// LOGIN ROUTES
$router->addRoute('GET', '/logout/', false, LoginController::class, 'logout');

$router->addRoute('GET', '/login/', false, LoginController::class, 'login');
$router->addRoute('POST', '/login/', false, LoginController::class, 'authenticate');

$router->addRoute('GET', '/new-account/', false, LoginController::class, 'newAccount');
$router->addRoute('POST', '/new-account/', false, LoginController::class, 'createAccount');

$router->addRoute('GET', '/forget-password/', false, LoginController::class, 'forgetPassword');
$router->addRoute('POST', '/forget-password/', false, LoginController::class, 'forgetPassword');

$router->addRoute('GET', '/forget-password/{token}/', false, LoginController::class, 'forgetPassword');
$router->addRoute('PUT', '/forget-password/{token}/', false, LoginController::class, 'resetPassword');

$router->addRoute('GET', '/missing-data/{userId}/', true, MissingDataController::class, 'index');
$router->addRoute('PUT', '/missing-data/{userId}/', true,MissingDataController::class, 'missingData');

// SYSTEM ROUTES
$router->addRoute('GET', '/dashboard/', true,DashboardController::class, 'index');

$router->addRoute('GET', '/users/', true,UserController::class, 'index');
$router->addRoute('PUT', '/users/{userId}/', true,UserController::class, 'updateUser');
$router->addRoute('GET', '/users/json/', true,UserController::class, 'json');
$router->addRoute('GET', '/users/json/{userId}/', true,UserController::class, 'json');

$middleware->autoRedirect();
$router->handleRequest();
