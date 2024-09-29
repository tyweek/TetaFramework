<?php

// routes.php

use TetaFramework\Http\RedirectResponse;
use TetaFramework\Routing\Router;
use TetaFramework\Http\Request;

$router = new Router();

$router->addRoute('GET', '/', 'App\Controllers\AuthController@toLogin');
$router->addRoute('GET', '/login', 'App\Controllers\AuthController@index');
$router->addRoute('GET', '/register', 'App\Controllers\AuthController@register');
$router->addRoute('GET', '/logout', 'App\Controllers\AuthController@logout');
$router->addRoute('GET', '/panel', 'App\Controllers\PanelController@index');

$router->addRoute('POST', '/register', 'App\Controllers\AuthController@register');
$router->addRoute('POST', '/login', 'App\Controllers\AuthController@login');

$router->addRoute('GET','/change-language', function(Request $request) {
    $lng = $request->get('lang');
    $language = new TetaFramework\Language\Language();
    $language->ChangeLang($lng);
    $uri = "/login";
    if($request->get('uri'))
        $uri = $request->get('uri');
    return new RedirectResponse($uri);
    // Actualiza la configuración del idioma del usuario
    // Puedes almacenar esta configuración en la sesión, en una cookie, etc.
});

return $router;
