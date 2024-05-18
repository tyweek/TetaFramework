<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TetaFramework\Router;

$request = Request::createFromGlobals();

$routes = require __DIR__ . '/../routes/web.php';
$router = new Router($routes);

$response = $router->handle($request);
$response->send();
