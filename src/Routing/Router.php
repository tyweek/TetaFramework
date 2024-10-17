<?php

namespace TetaFramework\Routing;

use TetaFramework\Http\Request;
use TetaFramework\Http\Response;
use TetaFramework\Language\Language;

class Router
{
    // Array que contiene todas las rutas registradas
    private $routes = [];
    private $errorPages = [];

    const BASE_PATH = __DIR__ . '/../../'; 

    public function __construct()
    {
        $this->setErrorPage(404,"src/Routing/templates/404");
        $this->setErrorPage(500,"src/Routing/templates/500");
    }
    

    /**
     * Añade una ruta a la lista de rutas.
     *
     * @param string $method El método HTTP (GET, POST, etc.)
     * @param string $path La ruta URL
     * @param callable|string $handler La función o controlador que manejará la ruta
     */
    public function addRoute($method, $path, $handler)
    {
        $this->routes[strtoupper($method)][] = [
            'path' => $path,
            'handler' => $handler
        ];
    }
    public function setErrorPage(int $statusCode, string $relativePath, $extension = ".php")
    {
        $fullPath = self::BASE_PATH . ltrim($relativePath, '/').$extension;
        $this->errorPages[$statusCode] = $fullPath;
    }
    // public function addRoute($method, $path, $handler)
    // {
    //     $this->routes[strtoupper($method)][$path] = $handler; // Almacena la ruta en el array $routes
    // }

    /**
     * Maneja la solicitud HTTP y devuelve una respuesta.
     *
     * @param Request $request La solicitud HTTP
     * @param Language $language El objeto de lenguaje para gestionar traducciones
     * @return Response La respuesta HTTP
     */
    public function handle(Request $request, Language $language): Response
    {
        try {
            $method = $request->getMethod();
            $path = $request->getPathInfo();
            if (isset($this->routes[$method])) {
                foreach ($this->routes[$method] as $route) {
                    $routePattern = preg_replace('/\:[a-zA-Z0-9\_\-]+/', '([a-zA-Z0-9\_\-]+)',  $route['path']);
                    if (preg_match('#^' . $routePattern . '$#', $path, $matches)) {
                        array_shift($matches);
                        $handler = $route['handler'];
                        if (is_callable($handler)) {
                            return call_user_func_array($handler, array_merge([$request], $matches));
                        } elseif (is_string($handler)) {
                            list($controller, $action) = explode('@', $handler);
                            if (class_exists($controller) && method_exists($controller, $action)) {
                                $controllerInstance = new $controller($language);
                                return call_user_func_array([$controllerInstance, $action], array_merge([$request], $matches));
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $th) {
            return $this->handleError(500);
        }
        return $this->handleError(404);
    }

    private function handleError(int $statusCode): Response
    {
        if (isset($this->errorPages[$statusCode])) {
            $errorContent = file_get_contents($this->errorPages[$statusCode]);
            return new Response($errorContent, $statusCode);
        }
        return new Response('Error ' . $statusCode, $statusCode);
    }
}
