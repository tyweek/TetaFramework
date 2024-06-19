<?php

namespace App\Controllers;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


use App\Models\Test;
use LDAP\Result;
use TetaFramework\Http\RedirectResponse;
use TetaFramework\Http\Response;
use TetaFramework\Http\Request;
use TetaFramework\View;

use function PHPSTORM_META\type;

class TestController extends Controller
{
    public function index(Request $request)
    {
        $items = Test::all();
        $content = View::render('Test', ['items' => $items]);
        return new Response($content);
    }

    public function store(Request $request)
    {
        $data = [
            'name' => 'brand'
        ];
        $newUser = Test::create($data);
        if ($newUser) {
            echo "El usuario se creó correctamente.";
        } else {
            echo "Hubo un problema al crear el usuario.";
        }
        return new Response("");
    }
}
