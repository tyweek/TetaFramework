<?php 

namespace TetaFramework\Template;

use TetaFramework\View;

class Template
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
    public function render($template, $data = [],$isComponent = false)
    {   
        $path = $isComponent ? "components/$template" : $template;
        // Renderiza la vista y captura su salida
        $content = View::render($path, $data);

        $content = $this->processDirectives($content, $this->variables);
    
        // Evalúa el contenido PHP
        eval(' ?>' . $content . '<?php ');
    }
    
    protected function processDirectives($content, $variables)
    {
        // Procesar las directivas como @import
        $content = $this->processImports($content);
        // Reemplaza las variables en el contenido
        $content = $this->replaceVariables($content, $this->variables);
        // Procesa bloques @php en la plantilla
        $content = $this->processPhp($content, $this->variables);
        // Procesa los bucles @for y foreach
        $content = $this->processLoops($content, $variables);
        // Procesa las condiciones en el contenido
        $content = $this->processConditions($content, $this->variables);

        $content = $this->replaceUndefinedVariables($content, $this->variables);

        

        return $content;
    }

    public function processImports($templateContent)
    {
         // Expresión regular para encontrar @import->nombre
        $pattern = '/@import->([a-zA-Z0-9_]+)/';

        // Función de callback para reemplazar las directivas
        $callback = function($matches) {
            $fragmentName = $matches[1];
            
            // Usar View::render para cargar el fragmento
            return View::render("partials/$fragmentName", []);
        };

        // Reemplazar las directivas en el contenido de la plantilla
        return preg_replace_callback($pattern, $callback, $templateContent);
    }

    protected function processLoops($content, $variables)
    {
        $content = $this->processForeach($content, $variables);
        $content = $this->processFor($content, $variables);
        return $content;
    }

    protected function processPhp($content, $variables)
    {
        // Patrón para encontrar bloques @php en la plantilla
        $pattern = '/@php\s*(.*?)\s*@endphp/s';
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
        
        // Iterar sobre cada bloque encontrado
        foreach ($matches as $match) {
            // Obtener el código PHP del bloque
            $phpCode = $match[1];

            // Evaluar el código PHP en el contexto actual de variables
            ob_start();
            eval($phpCode);
            $evaluatedPhpCode = ob_get_clean();

            // Reemplazar el bloque @php con el resultado evaluado
            $content = str_replace($match[0], $evaluatedPhpCode, $content);
        }

        return $content;
    }

    /**
     * Parsea la plantilla y evalúa llamadas a funciones en las variables.
     *
     * @param string $template El contenido de la plantilla.
     * @return string El contenido de la plantilla con las variables reemplazadas.
     */

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
            } elseif (is_object($value)) {
                foreach ($value as $property => $propertyValue) {
                    $placeholder = '{{ ' . $prefix . $key . '.' . $property . ' }}';
                    $content = str_replace($placeholder, $propertyValue, $content);
                }

                $reflection = new \ReflectionClass($value);
                $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
                foreach ($methods as $method) {
                    $methodName = $method->getName();
                    $placeholder = '{{ ' . $prefix . $key . '.' . $methodName . '() }}';
                    if (strpos($content, $placeholder) !== false) {
                        $result = call_user_func([$value, $methodName]);
                        $content = str_replace($placeholder, $result, $content);
                    }
                }
            } else {
                $placeholder = '{{ ' . $prefix . $key . ' }}';
                $content = str_replace($placeholder, $value, $content);
            }
        }

        return $content;
    }
    // protected function replaceVariables($content, $variables, $prefix = '')
    // {
    //     foreach ($variables as $key => $value) {
    //         if (is_array($value)) {
    //             $content = $this->replaceVariables($content, $value, $prefix . $key . '.');
    //         } elseif (is_object($value)) {
    //             // Reemplazar las propiedades del objeto
    //             foreach ($value as $property => $propertyValue) {
    //                 $placeholder = '{{ ' . $prefix . $key . '.' . $property . ' }}';
    //                 $content = str_replace($placeholder, $propertyValue, $content);
    //             }

    //             // Reemplazar las funciones del objeto
    //             $reflection = new \ReflectionClass($value);
    //             $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
    //             foreach ($methods as $method) {
    //                 $methodName = $method->getName();
    //                 $placeholder = '{{ ' . $prefix . $key . '.' . $methodName . '() }}';
    //                 if (strpos($content, $placeholder) !== false) {
    //                     $result = call_user_func([$value, $methodName]);
    //                     $content = str_replace($placeholder, $result, $content);
    //                 }
    //             }
    //         } else {
    //             $placeholder = '{{ ' . $prefix . $key . ' }}';
    //             $content = str_replace($placeholder, $value, $content);
    //         }
    //     }

       

    //     return $content;
    // }
    protected function replaceUndefinedVariables($content, $variables)
    {
        // Obtener todas las variables entre {{ }} en la vista
        preg_match_all('/{{\s*([^\s]+)\s*}}/', $content, $matches);

        // Iterar sobre las variables encontradas en la vista
        foreach ($matches[1] as $match) {
            $placeholder = '{{ ' . $match . ' }}';
            $key = trim($match);

            // Reemplazar el placeholder por una cadena vacía si la variable no existe en $variables
            if (!array_key_exists($key, $variables)) {
                $content = str_replace($placeholder, '', $content);
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
    
    protected function processCount($content, $variables)
    {
        // Patrón para encontrar llamadas a la función count()
        $pattern = '/{{\s*count\((.*?)\)\s*}}/';

        // Reemplaza las coincidencias usando una función de devolución de llamada
        $content = preg_replace_callback($pattern, function ($matches) use ($variables) {
            // Obtiene la expresión dentro de la llamada a count()
            $expression = trim($matches[1]);

            // Evalúa la expresión para obtener su valor
            $value = $this->evaluateExpression($expression, $variables);

            // Verifica si el valor es un array y aplica count(), o devuelve 0 si no es un array
            return is_array($value) ? count($value) : 0;
        }, $content);

        return $content;
    }

    // Otras funciones de la clase...

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
    
        // Convertir cadenas 'true' y 'false' a booleanos
        if ($condition === 'true') {
            $condition = true;
        } elseif ($condition === 'false') {
            $condition = false;
        }
        if ($value === 'true') {
            $value = true;
        } elseif ($value === 'false') {
            $value = false;
        }
    
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
    

    /**
     * Procesa los bucles @foreach en el contenido de la plantilla.
     *
     * @param string $content El contenido de la plantilla.
     * @param array $variables Las variables a utilizar en los bucles.
     * @return string El contenido con los bucles procesados.
     */
    protected function processForeach($content, $variables)
    {
        // Patrón para encontrar estructuras @foreach en la plantilla
        $pattern = '/{{\s*@foreach\s*\((.*?)\s+as\s+(\$\w+)(?:\s*=>\s*(\$\w+))?\)\s*}}(.*?){{\s*@endforeach\s*}}/s';
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
    
        // Iterar sobre todas las coincidencias encontradas
        foreach ($matches as $match) {
            // Obtener la expresión del bucle y el contenido dentro del bucle
            $arrayExpression = trim($match[1]);
            $valueVariable = trim($match[2]);
            $keyVariable = isset($match[3]) ? trim($match[3]) : null;
            $loopContent = trim($match[4]);
    
            // Evaluar la expresión del array
            $array = $this->evaluateExpression($arrayExpression, $variables);
    
            // Inicializar una cadena para almacenar el contenido del bucle procesado
            $processedLoopContent = '';
    
            // Verificar si el array es un array asociativo
            if (is_array($array) && count(array_filter(array_keys($array), 'is_string')) > 0) {
                // Iterar sobre el array asociativo
                foreach ($array as $key => $value) {
                    // Si el valor es un array, procesarlo como mensajes de error
                    if (is_array($value)) {
                        foreach ($value as $message) {
                            // Crear un nuevo conjunto de variables para el contenido del bucle
                            $loopVariables = array_merge($variables, [
                                $valueVariable => $key,
                                $keyVariable => $message
                            ]);
                            // Procesar el contenido del bucle con las nuevas variables
                            $processedLoopContent .= $this->replaceVariables($loopContent, $loopVariables);
                        }
                    } else {
                        // Si el valor no es un array, procesarlo normalmente
                        // Crear un nuevo conjunto de variables para el contenido del bucle
                        $loopVariables = array_merge($variables, [
                            $valueVariable => $value,
                            $keyVariable => $key
                        ]);
                        // Procesar el contenido del bucle con las nuevas variables
                        $processedLoopContent .= $this->replaceVariables($loopContent, $loopVariables);
                    }
                }
            } else {
                // Procesar el bucle normalmente
                // Iterar sobre el array
                foreach ($array as $key => $value) {
                    // Crear un nuevo conjunto de variables para el contenido del bucle
                    $loopVariables = array_merge($variables, [
                        $valueVariable => $value
                    ]);
                    // Si se proporciona una variable para la clave del bucle, agregarla a las variables del bucle
                    if ($keyVariable !== null) {
                        $loopVariables[$keyVariable] = $key;
                    }
                    // Procesar el contenido del bucle con las nuevas variables
                    $processedLoopContent .= $this->replaceVariables($loopContent, $loopVariables);
                }
            }
    
            // Reemplazar la estructura del bucle en el contenido con el contenido del bucle procesado
            $content = str_replace($match[0], $processedLoopContent, $content);
        }
    
        return $content;
    }
    protected function processFor($content, $variables)
    {
        // Patrón para encontrar estructuras @for en la plantilla
        // $pattern = '/{{\s*@for\s*\((.*?)\s*;\s*(.*?)\s*;\s*(.*?)\s*\)\s*}}(.*?){{\s*@endfor\s*}}/s';
        // $pattern = '/\s*@for\s*\((.*?)\s*;\s*(.*?)\s*;\s*(.*?)\s*\)\s*(.*?)\s*@endfor\s*/s';
        $pattern = '/\s*@for\s*\((.*?)\s*;\s*(.*?)\s*;\s*(.*?)\s*\)\s*(.*?)\s*@endfor\s*/s';
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $initial = trim($match[1]);
            $condition = trim($match[2]);
            $increment = trim($match[3]);
            $loopContent = $match[4];
            $initialValue = null;
            // Evaluar la expresión inicial
            eval('$initialValue = ' . $initial . ';');
    
            $processedLoopContent = '';
            $iterations = 0;
    
            // Evaluar la condición y el contenido del bucle
            while (eval('return ' . $condition . ';')) {
                $loopVariables = $variables;
                $loopVariables['i'] = $initialValue; // Asignar el valor inicial a la variable de control del bucle


                // $loopContent = $this->processConditions($loopContent, $loopVariables);
                // Procesar el contenido del bucle con las variables actualizadas
                $processedLoopContent .= $this->replaceVariables($loopContent, $loopVariables);
                
                // Evaluar la expresión de incremento
                eval($increment . ';');
                
                // Incrementar el valor inicial para la próxima iteración
                $initialValue++;
    
                $iterations++;
                if ($iterations > 1000) {
                    throw new \Exception("Se ha detectado un posible bucle infinito en la directiva @for.");
                }
            }
    
            // Reemplazar la estructura del bucle en el contenido con el contenido del bucle procesado
            $content = str_replace($match[0], $processedLoopContent, $content);
        }
    
    
        return $content;
    }
    

    /**
     * Verifica si un array es asociativo.
     *
     * @param array $array El array a verificar.
     * @return bool True si el array es asociativo, False en caso contrario.
     */
    protected function isAssociativeArray($array)
    {
        return is_array($array) && array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * Verifica si un array es de un tipo especial.
     *
     * @param array $array El array a verificar.
     * @return bool True si el array es de un tipo especial, False en caso contrario.
     */
    protected function isSpecialArray($array)
    {
        if (!is_array($array) || empty($array)) {
            return false;
        }

        foreach ($array as $value) {
            if (!is_array($value) || count($value) !== 1 || !is_array(reset($value))) {
                return false;
            }
        }

        return true;
    }


    /**
     * Evalúa una expresión para obtener su valor.
     *
     * @param string $expression La expresión a evaluar.
     * @param array $variables Las variables a utilizar en la evaluación.
     * @return mixed El resultado de la evaluación de la expresión.
     */
    protected function evaluateExpression($expression, $variables)
    {
        // Crear un contexto para evaluar la expresión con las variables
        $variablesForEval = $variables;
        extract($variablesForEval);

        // Evaluar la expresión en el contexto creado
        return eval("return ($expression);");
    }
    /**
 * Procesa la función is_array() en la vista.
 *
 * @param string $content El contenido de la plantilla.
 * @param array $variables Las variables a utilizar.
 * @return string El contenido con la función is_array() procesada.
 */
protected function processIsArray($content, $variables)
{
   // Patrón para encontrar llamadas a la función is_array()
   $pattern = '/{{\s*is_array\((.*?)\)\s*}}/';

   // Reemplaza las coincidencias usando una función de devolución de llamada
   $content = preg_replace_callback($pattern, function ($matches) use ($variables) {
       // Obtiene la expresión dentro de la llamada a is_array()
       $expression = trim($matches[1]);

       // Evalúa la expresión para obtener su valor
       $value = $this->evaluateExpression($expression, $variables);

       // Devuelve true si la expresión es un array, false en caso contrario
       return $value ? 'true' : 'false';
   }, $content);

   return $content;
}

}