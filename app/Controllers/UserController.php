<?php

namespace App\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;
use TetaFramework\View;

class UserController
{
    public function index()
    {
        $users = User::all();
        $content = View::render('users', ['users' => $users]);
        return new Response($content);
    }

    public function store()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => password_hash('password', PASSWORD_DEFAULT)
        ];

        $user = User::create($data);
        header("Location: /");
    }

    // Otros métodos para actualizar, eliminar, etc.
}
