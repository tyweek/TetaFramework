<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../bootstrap.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use TetaFramework\Router;

$request = Request::createFromGlobals();

$routes = require __DIR__ . '/../routes/web.php';
$router = new Router($routes);

try {
    // Lógica para manejar la solicitud y encontrar la ruta correspondiente
    $response = $route = $router->handle($request);

    // Ejecutar el controlador y el método correspondiente
    // ...

} catch (MethodNotAllowedException $e) {
    // Capturar la excepción cuando el método HTTP no está permitido
    $response = new Response('Error: Método HTTP no permitido para esta ruta', 405);
}
$response->send();
