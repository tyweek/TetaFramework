<?php

namespace TetaFramework\Routing;

use TetaFramework\Http\Request;
use TetaFramework\Http\Response;
use TetaFramework\Language\Language;

class Router
{
    // Array que contiene todas las rutas registradas
    private $routes = [];

    /**
     * Añade una ruta a la lista de rutas.
     *
     * @param string $method El método HTTP (GET, POST, etc.)
     * @param string $path La ruta URL
     * @param callable|string $handler La función o controlador que manejará la ruta
     */
    public function addRoute($method, $path, $handler)
    {
        $this->routes[strtoupper($method)][$path] = $handler; // Almacena la ruta en el array $routes
    }

    /**
     * Maneja la solicitud HTTP y devuelve una respuesta.
     *
     * @param Request $request La solicitud HTTP
     * @param Language $language El objeto de lenguaje para gestionar traducciones
     * @return Response La respuesta HTTP
     */
    public function handle(Request $request, Language $language): Response
    {
        $method = $request->getMethod(); // Obtiene el método HTTP de la solicitud
        $path = $request->getPathInfo(); // Obtiene la ruta URL de la solicitud

        // Comprueba si la ruta y el método existen en las rutas registradas
        if (isset($this->routes[$method][$path])) {
            $handler = $this->routes[$method][$path]; // Obtiene el manejador correspondiente

            // Si el manejador es una función callable, la llama con la solicitud como parámetro
            if (is_callable($handler)) {
                return call_user_func($handler, $request);
            } 
            // Si el manejador es una cadena, asume el formato 'Controller@action'
            elseif (is_string($handler)) {
                list($controller, $action) = explode('@', $handler); // Divide la cadena en controlador y acción

                // Comprueba si la clase del controlador y el método existen
                if (class_exists($controller) && method_exists($controller, $action)) {
                    $controllerInstance = new $controller($language); // Crea una instancia del controlador
                    return call_user_func([$controllerInstance, $action], $request); // Llama a la acción del controlador
                }
            }
        }

        // Si la ruta no se encuentra, devuelve una respuesta 404
        return new Response('Not Found', 404);
    }
}
