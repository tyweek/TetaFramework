<?php

// routes.php

use TetaFramework\Http\RedirectResponse;
use TetaFramework\Routing\Router;
use TetaFramework\Http\Request;

$router = new Router();

$router->addRoute('GET', '/', 'App\Controllers\HomeController@index');
$router->addRoute('GET', '/login', 'App\Controllers\AuthController@index');
$router->addRoute('GET', '/register', 'App\Controllers\AuthController@register');
$router->addRoute('GET', '/user', 'App\Controllers\UserController@index');
$router->addRoute('GET', '/logout', 'App\Controllers\AuthController@logout');


$router->addRoute('POST', '/register', 'App\Controllers\AuthController@register');
$router->addRoute('POST', '/login', 'App\Controllers\AuthController@login');


$router->addRoute('GET','/change-language', function(Request $request) {
    $lng = $request->get('lang');
    $language = new TetaFramework\Language\Language();
    $language->ChangeLang($lng);
    return new RedirectResponse("/");
    // Actualiza la configuración del idioma del usuario
    // Puedes almacenar esta configuración en la sesión, en una cookie, etc.
});

return $router;