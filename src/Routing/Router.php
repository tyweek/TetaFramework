<?php

// src/Routing/Router.php
namespace TetaFramework\Routing;

use TetaFramework\Http\Request;
use TetaFramework\Http\Response;
use TetaFramework\Language\Language;

class Router
{
    private $routes = [];

    public function addRoute($method, $path, $handler)
    {
        $this->routes[strtoupper($method)][$path] = $handler;
    }

    public function handle(Request $request,Language $language): Response
    {
        $method = $request->getMethod();
        $path = $request->getPathInfo();

        if (isset($this->routes[$method][$path])) {
            $handler = $this->routes[$method][$path];

            if (is_callable($handler)) {
                return call_user_func($handler, $request);
            } elseif (is_string($handler)) {
                list($controller, $action) = explode('@', $handler);
                if (class_exists($controller) && method_exists($controller, $action)) {
                    $controllerInstance = new $controller($language);
                    return call_user_func([$controllerInstance, $action], $request);
                }
            }
        }

        return new Response('Not Found', 404);
    }
}
