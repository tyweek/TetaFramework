<?php

namespace TetaFramework;

// Definición de la clase Config dentro del namespace TetaFramework
class Config
{
    // Variable estática privada para almacenar la configuración cargada
    private static $config;

    /**
     * Obtiene un valor de configuración basado en una clave dada.
     *
     * @param string $key La clave de configuración a buscar.
     * @param mixed $default (opcional) El valor predeterminado a devolver si la clave no se encuentra.
     * @return mixed El valor de configuración correspondiente a la clave, o el valor predeterminado si la clave no existe.
     */
    public static function get($key, $default = null)
    {
        // Verifica si la configuración aún no ha sido cargada
        if (!self::$config) {
            // Carga la configuración desde el archivo app.php en el directorio de configuración
            self::$config = require __DIR__ . '/../config/app.php';
        }
        // Devuelve el valor de configuración correspondiente a la clave, o el valor predeterminado si la clave no existe
        return self::$config[$key] ?? $default;
    }
}
