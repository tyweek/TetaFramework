<?php 

// src/Template/Template.php
namespace TetaFramework\Template;
use TetaFramework\View;

class Template
{
    protected $variables = [];

    public function assign($key, $value)
    {
        $this->variables[$key] = $value;
    }

    public function render($template,$data = [])
    {
        $content = View::render($template,$data);
        $content = $this->replaceVariables($content, $this->variables);
        $content = $this->processConditions($content, $this->variables);
        $content = $this->parseTemplate($content, $this->variables);
        eval(' ?>' . $content . '<?php ');
    }
    protected function parseTemplate($template)
    {
        // Expresión regular para encontrar variables y funciones
        $pattern = '/{{\s*([a-zA-Z_][a-zA-Z0-9_]*(?:->[a-zA-Z_][a-zA-Z0-9_]*)*)(?:\(\))?(\([^{}]+\))?}}/';

        // Reemplazar variables y funciones en el template
        $content = preg_replace_callback($pattern, function ($matches) {
            $variableName = $matches[1];

            // Verificar si la variable existe
            if (isset($this->variables[$variableName])) {
                $value = $this->variables[$variableName];

                // Si hay paréntesis, asumimos que es una función
                if (isset($matches[2]) && !empty($matches[2])) {
                    // Extraer el nombre de la función y los argumentos
                    preg_match('/([a-zA-Z_][a-zA-Z0-9_]*)(\([^{}]+\))/', $matches[2], $functionMatches);
                    $functionName = $functionMatches[1];
                    $arguments = eval("return {$functionMatches[2]};"); // Evaluar los argumentos

                    // Verificar si la función existe en el objeto
                    if (is_callable([$value, $functionName])) {
                        // Llamar a la función y devolver el resultado
                        return call_user_func_array([$value, $functionName], $arguments);
                    } else {
                        // La función no existe, devolver una cadena vacía
                        return '';
                    }
                } else {
                    // Si no hay paréntesis, simplemente devolver el valor de la variable
                    return $value;
                }
            } else {
                // La variable no existe, devolver una cadena vacía
                return '';
            }
        }, $template);

        return $content;
    }
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
    
    protected function processConditions($content, $variables)
    {
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

    protected function evaluateCondition($condition, $operator, $value, $variables)
    {
        $condition = $this->replaceVariables($condition, $variables);
        $value = $this->replaceVariables($value, $variables);
        // Evaluar la expresión
        switch ($operator) {
            case '==':
                return $condition === $value; // Usar "===" para una comparación estricta
            case '!=':
                return $condition !== $value; // Usar "!==" para una comparación estricta
            case '<':
                return $condition < $value;
            case '>':
                return $condition > $value;
            case '<=':
                return $condition <= $value;
            case '>=':
                return $condition >= $value;
            default:
                return false;
        }
    }
}
