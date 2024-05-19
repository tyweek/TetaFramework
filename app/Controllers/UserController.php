<?php

namespace App\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;
use TetaFramework\View;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    public function index()
    {
        $redirect = $this->checkSession();
        if ($redirect !== null) {
            return $redirect;
        }

        $users = User::all();
        $content = View::render('users', ['users' => $users]);
        return new Response($content);
    }

    public function store(Request $request)
    {
        $data = [
            'name' => 'Brando Reverol',
            'email' => 'brando@g.com',
            'password' => password_hash('password', PASSWORD_DEFAULT)
        ];

        $user = User::create($data);
        header("Location: /");
    }

    // Otros métodos para actualizar, eliminar, etc.
}
