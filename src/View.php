<?php

namespace TetaFramework;

// Definición de la clase View dentro del namespace TetaFramework
class View
{
    /**
     * Renderiza una vista y devuelve el contenido generado.
     *
     * @param string $view El nombre de la vista a renderizar (sin la extensión .php).
     * @param array $data (opcional) Un array asociativo de datos que se extraerán como variables dentro de la vista.
     * @return string El contenido HTML renderizado de la vista.
     */
    public static function render($view, $data = [])
    {
        // Extrae los elementos del array $data como variables individuales.
        // Por ejemplo, si $data = ['name' => 'John'], se crea una variable $name con el valor 'John'.
        extract($data);
        
        // Inicia la captura de la salida en un buffer.
        ob_start();
        
        // Incluye el archivo de la vista. La ruta se construye concatenando el nombre de la vista con la ruta del directorio de vistas.
        // Por ejemplo, si $view = 'home', se incluirá el archivo '/path/to/views/home.php'.
        require __DIR__ . '/../views/' . $view . '.php';
        
        // Devuelve el contenido capturado del buffer como una cadena.
        return ob_get_clean();
    }
}
