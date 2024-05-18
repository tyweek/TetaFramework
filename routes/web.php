<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$routes = new RouteCollection();

$routes->add('home', new Route('/', [
    '_controller' => 'App\\Controllers\\HomeController::index',
], [], [], '', [], ['GET']));
$routes->add('users', new Route('/users', [
    '_controller' => 'App\\Controllers\\UserController::index',
], [], [], '', [], ['GET']));
$routes->add('addusers', new Route('/users/add', [
    '_controller' => 'App\\Controllers\\UserController::store',
], [], [], '', [], ['POST']));


return $routes;
