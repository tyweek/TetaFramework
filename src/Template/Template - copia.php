<?php 

namespace TetaFramework\Template;

use TetaFramework\View;

class zxczxcjkj12i41048
{
    // Array protegido que contiene las variables asignadas para la plantilla
    protected $variables = [];

    /**
     * Asigna un valor a una clave específica en el array de variables.
     *
     * @param string $key La clave de la variable.
     * @param mixed $value El valor de la variable.
     */
    public function assign($key, $value)
    {
        $this->variables[$key] = $value;
    }
    

    /**
     * Renderiza una plantilla, reemplaza variables y procesa condiciones.
     *
     * @param string $template Nombre de la plantilla.
     * @param array $data Datos adicionales a pasar a la vista.
     */
    public function render($template, $data = [])
    {
        // Renderiza la vista y captura su salida
        $content = View::render($template, $data);
        
        // Reemplaza las variables en el contenido
        $content = $this->replaceVariables($content, $this->variables);
        
        // Procesa las condiciones en el contenido
        $content = $this->processConditions($content, $this->variables);

        // Procesa la plantilla para manejar llamadas a funciones de variables
        $content = $this->parseTemplate($content, $this->variables);
        
        // Evalúa el contenido PHP
        eval(' ?>' . $content . '<?php ');
    }

    /**
     * Parsea la plantilla y evalúa llamadas a funciones en las variables.
     *
     * @param string $template El contenido de la plantilla.
     * @return string El contenido de la plantilla con las variables reemplazadas.
     */
    protected function parseTemplate($template)
    {
        // Patrón para encontrar variables y posibles llamadas a funciones
        $pattern = '/{{\s*([a-zA-Z_][a-zA-Z0-9_]*(?:->[a-zA-Z_][a-zA-Z0-9_]*)*)(?:\(\))?(\([^{}]+\))?}}/';

        // Reemplaza las coincidencias usando una función de devolución de llamada
        $content = preg_replace_callback($pattern, function ($matches) {
            $variableName = $matches[1];

            // Verifica si la variable existe
            if (isset($this->variables[$variableName])) {
                $value = $this->variables[$variableName];

                // Si hay una llamada a función, evalúa y llama a la función
                if (isset($matches[2]) && !empty($matches[2])) {
                    preg_match('/([a-zA-Z_][a-zA-Z0-9_]*)(\([^{}]+\))/', $matches[2], $functionMatches);
                    $functionName = $functionMatches[1];
                    $arguments = eval("return {$functionMatches[2]};"); 

                    if (is_callable([$value, $functionName])) {
                        return call_user_func_array([$value, $functionName], $arguments);
                    } else {
                        return '';
                    }
                } else {
                    return $value;
                }
            } else {
                return '';
            }
        }, $template);

        return $content;
    }

    /**
     * Reemplaza las variables en el contenido de la plantilla.
     *
     * @param string $content El contenido de la plantilla.
     * @param array $variables Las variables a reemplazar.
     * @param string $prefix Prefijo para las claves de las variables.
     * @return string El contenido con las variables reemplazadas.
     */
    protected function replaceVariables($content, $variables, $prefix = '')
    {
        foreach ($variables as $key => $value) {
            if (is_array($value)) {
                $content = $this->replaceVariables($content, $value, $prefix . $key . '.');
            } else {
                $placeholder = '{{ ' . $prefix . $key . ' }}';
                $content = str_replace($placeholder, htmlspecialchars($value, ENT_QUOTES, 'UTF-8'), $content);
            }
        }
        return $content;
    }

    /**
     * Procesa las condiciones @if y @else en el contenido de la plantilla.
     *
     * @param string $content El contenido de la plantilla.
     * @param array $variables Las variables a utilizar en las condiciones.
     * @return string El contenido con las condiciones procesadas.
     */
    protected function processConditions($content, $variables)
    {
        // Patrón para encontrar condiciones @if y @else
        $pattern = '/@if\((.*?)\s*(==|!=|<=|>=|<|>)\s*(.*?)\)\s*(.*?)\s*(?:@else\s*(.*?))?@endif/s';
        preg_match_all($pattern, $content, $matches);

        for ($i = 0; $i < count($matches[0]); $i++) {
            $condition = trim($matches[1][$i]);
            $operator = $matches[2][$i];
            $value = trim($matches[3][$i]);
            $body = trim($matches[4][$i]);
            $elseBody = isset($matches[5][$i]) ? trim($matches[5][$i]) : '';

            if ($this->evaluateCondition($condition, $operator, $value, $variables)) {
                $content = str_replace($matches[0][$i], $body, $content);
            } else {
                $content = str_replace($matches[0][$i], $elseBody, $content);
            }
        }

        return $content;
    }

    /**
     * Evalúa una condición para determinar su validez.
     *
     * @param string $condition La condición a evaluar.
     * @param string $operator El operador de comparación.
     * @param string $value El valor a comparar.
     * @param array $variables Las variables a utilizar en la evaluación.
     * @return bool El resultado de la evaluación de la condición.
     */
    protected function evaluateCondition($condition, $operator, $value, $variables)
    {
        // Reemplaza variables en la condición y el valor
        $condition = $this->replaceVariables($condition, $variables);
        $value = $this->replaceVariables($value, $variables);

        // Evalúa la condición basada en el operador
        switch ($operator) {
            case '==':
                return $condition === $value; // Devuelve verdadero si $condition es igual a $value
            case '!=':
                return $condition !== $value; // Devuelve verdadero si $condition no es igual a $value
            case '<':
                return $condition < $value; // Devuelve verdadero si $condition es menor que $value
            case '>':
                return $condition > $value; // Devuelve verdadero si $condition es mayor que $value
            case '<=':
                return $condition <= $value; // Devuelve verdadero si $condition es menor o igual que $value
            case '>=':
                return $condition >= $value; // Devuelve verdadero si $condition es mayor o igual que $value
            default:
                return false; // Devuelve falso para cualquier otro operador no reconocido
        }
    }
}
