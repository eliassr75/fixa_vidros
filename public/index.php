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

// Rotas API - GET
$router->addRoute('GET', '/api/users/', UserController::class, 'getAllUsers');

// Rotas API - POST
$router->addRoute('POST', '/api/users/', UserController::class, 'createUser');
$router->addRoute('POST', '/api/menu_items/', MenuController::class, 'createMenuItem');

// Rotas API - PUT
$router->addRoute('PUT', '/api/menu_items/', MenuController::class, 'updateMenuItem');

// Rotas API - DELETE
$router->addRoute('DELETE', '/api/menu_items/', MenuController::class, 'removeMenuItem');

// ROTAS USERS - GET
$router->addRoute('GET', '/users/', UserController::class, 'showAllUsers');
$router->addRoute('GET', '/users/{userId}/post/{newId}/', UserController::class, 'getUserById');


//ROTAS ANIMES - GET
$router->addRoute('GET', '/animes/', AnimeController::class, 'showAllAnimes');
$router->addRoute('GET', '/animes/all/', AnimeController::class, 'getAllAnimes');
$router->addRoute('GET', '/anime/create/{malId}/', AnimeController::class, 'createAnime');
$router->addRoute('GET', '/anime/update/{malId}/', AnimeController::class, 'updateAnime');
$router->addRoute('GET', '/anime/characters/{malId}/', AnimeController::class, 'getCharacters');
$router->addRoute('GET', '/anime/staff/{malId}/', AnimeController::class, 'getStaff');


$router->handleRequest();


//switch ($_SERVER['REQUEST_METHOD']){
//    case 'GET':
//        switch ($url){
//            case '/api/users':
//                $controller = new UserController();
//                $controller->getAllUsers();
//                break;
//            case '/users':
//                $controller = new UserController();
//                $controller->showAllUsers();
//                break;
//            default:
//                http_response_code(404);
//                echo json_encode(['message' => 'Endpoint não encontrado'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
//                break;
//        }
//        break;
//    case 'POST':
//        switch ($url){
//            case '/api/users':
//                $controller = new UserController();
//                $controller->createUser();
//                break;
//            default:
//                http_response_code(404);
//                echo json_encode(['message' => 'Endpoint não encontrado'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
//                break;
//        }
//        break;
//    /** @noinspection PhpDuplicateSwitchCaseBodyInspection */
//    case 'PUT':
//        switch ($url){
//            default:
//                http_response_code(404);
//                echo json_encode(['message' => 'Endpoint não encontrado'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
//                break;
//        }
//        break;
//    /** @noinspection PhpDuplicateSwitchCaseBodyInspection */
//    case 'DELETE':
//        switch ($url){
//            default:
//                http_response_code(404);
//                echo json_encode(['message' => 'Endpoint não encontrado'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
//                break;
//        }
//        break;
//    default:
//        http_response_code(405);
//        echo json_encode(["error" => "Método não permitido"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
//        break;
//}
