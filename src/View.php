<?php

namespace TetaFramework;

class View
{
    protected static $variables = []; // Almacenar variables

    /**
     * Asigna una variable.
     *
     * @param string $key La clave de la variable.
     * @param mixed $value El valor de la variable.
     */
    public static function assign($key, $value)
    {
        self::$variables[$key] = $value;
    }

    /**
     * Vacía las variables almacenadas.
     */
    public static function clear()
    {
        self::$variables = [];
    }

    /**
     * Renderiza una vista y devuelve el contenido generado.
     *
     * @param string $view El nombre de la vista a renderizar (sin la extensión .php).
     * @return string El contenido HTML renderizado de la vista.
     * @throws \Exception Si se detecta un bucle infinito.
     */
    public static function render($view)
    {
        extract(self::$variables); // Extraer las variables

        ob_start();

        // Cargar la plantilla inicial
        $template = file_get_contents(__DIR__ . '/../views/' . $view . '.php');

        // Procesar directivas hasta que no haya cambios o se alcance el límite de iteraciones
        $iterationLimit = 100; // Límite de iteraciones para evitar bucles infinitos
        $iterations = 0;

        do {
            if ($iterations++ >= $iterationLimit) {
                throw new \Exception('Se ha alcanzado el límite de iteraciones al procesar la plantilla. Posible bucle infinito detectado.');
            }

            $newTemplate = self::processDirectives($template);
            // Si no hay cambios, salir del bucle
            if ($newTemplate === $template) {
                break;
            }
            $template = $newTemplate;
        } while (true);

        try {
            eval('?>' . $template);
        } catch (\Throwable $e) {
            echo '<div class="error">Error en la ejecución de la plantilla: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }

        self::clear(); // Limpiar las variables después de renderizar

        return ob_get_clean(); // Devolver el contenido generado
    }

    // Procesar las directivas dentro de la plantilla
    protected static function processDirectives($content)
    {
        $originalContent = $content; // Guardar el contenido original para comparar

        $content = self::processPhpDirectives($content);
        $content = self::processForeach($content);
        $content = self::processIf($content);
        $content = self::processElse($content);
        $content = self::processFor($content);
        $content = self::processPhpCode($content);

        // Si el contenido ha cambiado, devolverlo, de lo contrario devolver el original
        return $content !== $originalContent ? $content : $originalContent;
    }

    protected static function processPhpDirectives($content)
    {
        return preg_replace('/{{\s*(.*?)\s*}}/', '<?php echo htmlspecialchars($1, ENT_QUOTES); ?>', $content);
    }

    protected static function processForeach($content)
    {
        return preg_replace_callback('/@foreach\s*\(\s*(.*?)\s*\)\s*(.*?)@endforeach/s', function ($matches) {
            return '<?php foreach (' . trim($matches[1]) . '): ?>' . $matches[2] . '<?php endforeach; ?>';
        }, $content);
    }

    protected static function processIf($content)
    {
        return preg_replace_callback('/@if\s*\(\s*(.*?)\s*\)\s*(.*?)@endif/s', function ($matches) {
            return '<?php if (' . trim($matches[1]) . '): ?>' . $matches[2] . '<?php endif; ?>';
        }, $content);
    }

    protected static function processElse($content)
    {
        return str_replace('@else', '<?php else: ?>', $content);
    }

    protected static function processFor($content)
    {
        return preg_replace_callback('/@for\s*\((.*?)\)\s*(.*?)@endfor/s', function ($matches) {
            return '<?php for (' . trim($matches[1]) . '): ?>' . $matches[2] . '<?php endfor; ?>';
        }, $content);
    }

    protected static function processPhpCode($content)
    {
        return preg_replace('/@php\s*(.*?)\s*@endphp/s', '<?php $1 ?>', $content);
    }
}
