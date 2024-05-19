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

$routes->add('add_user', new Route('/users/add', [
    '_controller' => 'App\\Controllers\\UserController::store',
], [], [], '', [], ['POST']));

$routes->add('login', new Route('/login', [
    '_controller' => 'App\\Controllers\\AuthController::index',
], [], [], '', [], ['GET']));

$routes->add('login_post', new Route('/login', [
    '_controller' => 'App\\Controllers\\AuthController::login',
], [], [], '', [], ['POST']));

$routes->add('login_post', new Route('/login', [
    '_controller' => 'App\\Controllers\\AuthController::login',
], [], [], '', [], ['POST']));
$routes->add('logout', new Route('/logout', [
    '_controller' => 'App\\Controllers\\AuthController::logout',
], [], [], '', [], ['GET']));


return $routes;
