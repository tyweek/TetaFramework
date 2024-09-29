<?php
// Establece la zona horaria predeterminada a 'America/Caracas'
date_default_timezone_set('America/Caracas');

// Establece el nivel de reporte de errores para solo mostrar errores fatales y errores de análisis
error_reporting(E_ERROR | E_PARSE);

try {
    // Intenta cargar la configuración de la base de datos desde el archivo '../config/database.php'
    $config = require_once './../config/database.core.php';
} catch (\Throwable $th) {
    // Si ocurre un error (por ejemplo, el archivo no existe en la ruta anterior),
    // intenta cargar la configuración de la base de datos desde el archivo './config/database.php'
    $config = require_once './config/database.core.php';
}

// Usa la clase DatabaseManagdf de TetaFramework\Database
use TetaFramework\Database\DatabaseManager;

// Agrega una conexión a la base de datos usando la configuración cargada
DatabaseManager::addConnection($config);
DatabaseManager::setAsGlobal();

