<?php

namespace TetaFramework;

// Definición de la clase Config dentro del namespace TetaFramework
class Config
{
    // Variable estática privada para almacenar la configuración cargada
    private static $config;
    // Ruta al archivo de configuración
    private static $configFile = __DIR__ . '/../config/app.php';

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
            // Carga la configuración desde el archivo
            self::$config = require self::$configFile;
        }
        // Devuelve el valor de configuración correspondiente a la clave, o el valor predeterminado si la clave no existe
        return self::$config[$key] ?? $default;
    }

    /**
     * Establece un valor de configuración basado en una clave dada.
     *
     * @param string $key La clave de configuración a establecer.
     * @param mixed $value El valor que se va a guardar para la clave de configuración.
     */
    public static function set($key, $value)
    {
        // Carga la configuración si no ha sido cargada aún
        if (!self::$config) {
            self::$config = require self::$configFile;
        }

        // Establece el nuevo valor en la configuración
        self::$config[$key] = $value;

        // Guarda la configuración actualizada de vuelta en el archivo
        self::save();
    }

    /**
     * Guarda la configuración actualizada en el archivo de configuración.
     */
    private static function save()
    {
        // Prepara el contenido a escribir en el archivo
        $content = "<?php\n\nreturn " . var_export(self::$config, true) . ";\n";

        // Escribe el contenido en el archivo
        file_put_contents(self::$configFile, $content);
    }
}
