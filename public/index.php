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
$router->addRoute('GET', '', false,  "login", false,LoginController::class, 'redirectToLogin');
$router->addRoute('GET', '/', false,  "login", false,LoginController::class, 'redirectToLogin');
$router->addRoute('GET', '/error/{codeError}/', false,  "error", false,ErrorController::class, 'errorPage');

// LOGIN ROUTES
$router->addRoute('GET', '/logout/', false,  "logout", false,LoginController::class, 'logout');

$router->addRoute('GET', '/login/', false,  "login", false,LoginController::class, 'login');
$router->addRoute('POST', '/login/', false,  "login", false,LoginController::class, 'authenticate');

$router->addRoute('GET', '/new-account/', false,  "login", false,LoginController::class, 'newAccount');
$router->addRoute('POST', '/new-account/', false,  "global", false,LoginController::class, 'createAccount');
$router->addRoute('GET', '/new-account/continue/{token}/', false, "global", false,LoginController::class, 'linkManager');

$router->addRoute('GET', '/forget-password/', false,  "login", false,LoginController::class, 'forgetPassword');
$router->addRoute('POST', '/forget-password/', false,  "login", false,LoginController::class, 'forgetPassword');

$router->addRoute('GET', '/forget-password/{token}/', false,  "login", false,LoginController::class, 'forgetPassword');
$router->addRoute('PUT', '/forget-password/{token}/', false,  "login", false,LoginController::class, 'resetPassword');

$router->addRoute('GET', '/missing-data/{userId}/', true,  "system", [1, 2, 3, 4],MissingDataController::class, 'index');
$router->addRoute('PUT', '/missing-data/{userId}/', true, "system", [1, 2, 3, 4],MissingDataController::class, 'missingData');

// SYSTEM ROUTES
$router->addRoute('GET', '/dashboard/', true, "system", [1, 2, 3, 4],DashboardController::class, 'index');

$router->addRoute('GET', '/users/', true, "system", [1],UserController::class, 'index');
$router->addRoute('PUT', '/users/{userId}/', true, "system", [1],UserController::class, 'updateUser');
$router->addRoute('PUT', '/users/change/{userId}/', true, "system", [1],UserController::class, 'changeUser');
$router->addRoute('GET', '/users/json/', true, "system", [1],UserController::class, 'json');
$router->addRoute('GET', '/users/json/{userId}/', true, "system", [1],UserController::class, 'json');

$router->addRoute('GET', '/clients/', true, "system", [1, 2, 3],ClientController::class, 'index');
$router->addRoute('GET', '/clients/json/', true, "system", [1, 2, 3],ClientController::class, 'json');

$router->addRoute('GET', '/client/new/', true, "system", [1, 2, 3],ClientController::class, 'getClient');
$router->addRoute('POST', '/client/new/', true, "system", [1, 2, 3],ClientController::class, 'newClient');
$router->addRoute('GET', '/client/{clientId}/', true, "system", [1, 2, 3],ClientController::class, 'getClient');
$router->addRoute('GET', '/client/json/{clientId}/', true, "system", [1, 2, 3],ClientController::class, 'json');
$router->addRoute('PUT', '/client/{clientId}/', true, "system", [1, 2, 3],ClientController::class, 'updateClient');
$router->addRoute('PUT', '/client/change/{clientId}/', true, "system", [1, 2, 3],ClientController::class, 'changeClient');

$middleware->autoRedirect();
$router->handleRequest();
