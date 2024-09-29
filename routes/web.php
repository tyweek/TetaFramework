<?php

// routes.php

use TetaFramework\Http\RedirectResponse;
use TetaFramework\Routing\Router;
use TetaFramework\Http\Request;

$router = new Router();

// $router->addRoute('GET', '/', 'App\Controllers\AuthController@toLogin');

// $router->addRoute('POST','/change-language', function(Request $request) {
//     $lng = $request->get('lang');
//     $language = new TetaFramework\Language\Language();
//     $language->ChangeLang($lng);
//     $uri = "/login";
//     if($request->get('uri'))
//         $uri = $request->get('uri');
//     return new RedirectResponse($uri);
//     // Actualiza la configuración del idioma del usuario
//     // Puedes almacenar esta configuración en la sesión, en una cookie, etc.
// });

return $router;
