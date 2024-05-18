<?php

namespace TetaFramework;

class View
{
    public static function render($view, $data = [])
    {
        extract($data);
        ob_start();
        require __DIR__ . '/../views/' . $view . '.php';
        return ob_get_clean();
    }
}
