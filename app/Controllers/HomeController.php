<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Response;
use TetaFramework\View;

class HomeController
{
    public function index()
    {
        $content = View::render('home', ['message' => 'Hello from HomeController']);
        return new Response($content);
    }
}
