<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../bootstrap.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;


$request = Request::createFromGlobals();

$routes = require __DIR__ . '/../routes/web.php';

$context = new RequestContext();
$request = Request::createFromGlobals();
$context->fromRequest($request);
$matcher = new UrlMatcher($routes, $context);

try {
    // Encuentra la ruta correspondiente para la solicitud actual
    $parameters = $matcher->match($request->getPathInfo());

    // Ejecuta el controlador correspondiente
    $controller = $parameters['_controller'];
    $action = explode('::', $controller);
    $class = new $action[0]();
    $response = call_user_func_array([$class, $action[1]], [$request]);

} catch (ResourceNotFoundException $e) {
    $response = new Response('Página no encontrada', 404);
} catch (MethodNotAllowedException $e) {
    $response = new Response('Método HTTP no permitido para esta ruta', 405);
}

// Envía la respuesta al cliente
$response->send();
