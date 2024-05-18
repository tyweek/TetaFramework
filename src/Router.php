<?php

namespace TetaFramework;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Request;

class Router
{
    private $routes;

    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
    }

    public function handle(Request $request)
    {
        $matcher = new \Symfony\Component\Routing\Matcher\UrlMatcher($this->routes, new \Symfony\Component\Routing\RequestContext());
        $parameters = $matcher->match($request->getPathInfo());

        $controller = $parameters['_controller'];
        list($class, $method) = explode('::', $controller);

        $controllerInstance = new $class();
        return call_user_func_array([$controllerInstance, $method], []);
    }
}
