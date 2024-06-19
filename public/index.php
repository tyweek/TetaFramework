<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../bootstrap.php';

use TetaFramework\Http\Request;
use TetaFramework\Http\Response;

// Crea una instancia del sistema de lenguaje
$language = new TetaFramework\Language\Language("es");

// Configura el idioma predeterminado (puedes leer esta configuración desde un archivo de configuración)
$language->fromSession();

// Incluimos el archivo de rutas
$request = Request::createFromGlobals();
$router = require_once __DIR__ . '/../routes/web.php';
$response = $router->handle($request,$language);
$response->send();
