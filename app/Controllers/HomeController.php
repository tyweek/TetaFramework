<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use TetaFramework\View;

class HomeController extends Controller
{
    public function index()
    {
        $content = View::render('home', ['message' => 'Hello from HomeController']);
        return new Response($content);
    }
}
