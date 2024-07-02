<?php

namespace App;

class Router
{
    private $routes = [];

    public function addRoute($method, $url, $controller, $action)
    {
        // Transformar URL em regex e identificar parâmetros dinâmicos
        $url = rtrim($url, '/') . '/';
        $url = preg_replace('/\//', '\\/', $url);
        $url = preg_replace('/\{(\w+)\}/', '(?P<$1>[^\/]+)', $url);
        $url = '/^' . $url . '$/';

        $this->routes[] = [
            'method' => $method,
            'url' => $url,
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function handleRequest()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = rtrim($uri, '/') . '/';

        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($route['method'] == $method && preg_match($route['url'], $uri, $matches)) {
                $controllerName = $route['controller'];
                $controller = new $controllerName();

                // Remover itens que não são parâmetros da URL
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                call_user_func_array([$controller, $route['action']], $params);
                return;
            }
        }

        header("HTTP/1.1 404 Not Found");
        echo '404 - Not Found';
    }
}
