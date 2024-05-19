<?php
// Establece la zona horaria predeterminada a 'America/Caracas'
date_default_timezone_set('America/Caracas');

// Establece el nivel de reporte de errores para solo mostrar errores fatales y errores de análisis
error_reporting(E_ERROR | E_PARSE);

try {
    // Intenta cargar la configuración de la base de datos desde el archivo '../config/database.php'
    $config = require_once './../config/database.php';
} catch (\Throwable $th) {
    // Si ocurre un error (por ejemplo, el archivo no existe en la ruta anterior),
    // intenta cargar la configuración de la base de datos desde el archivo './config/database.php'
    $config = require_once './config/database.php';
}

// Usa la clase Manager de Illuminate\Database como Capsule
use Illuminate\Database\Capsule\Manager as Capsule;

// Crea una nueva instancia de Capsule (Eloquent ORM)
$capsule = new Capsule;

// Agrega una conexión a la base de datos usando la configuración cargada
$capsule->addConnection($config);

// Establece Capsule como global para que todas las partes del proyecto puedan acceder a esta instancia de Eloquent
$capsule->setAsGlobal();

// Inicializa Eloquent ORM
$capsule->bootEloquent();
