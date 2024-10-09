<?php

// src/Http/Auth.php
namespace TetaFramework\Http;

use TetaFramework\Http\Session;
use App\Models\User;

class Auth
{
    protected $session;
    
    public function __construct()
    {
        $this->session = new Session();
    }

    // Verificar si el usuario está autenticado
    public function check()
    {
        return $this->session->has('user_id');
    }

    // Intentar iniciar sesión
    public function attempt($username, $password)
    {
        // Buscar el usuario en la base de datos (aquí debes implementar la lógica de búsqueda)
        $user = (new User())->findBy('username',$username);

        if ($user && password_verify($password, $user->password)) {
            // Establecer la sesión con los datos del usuario
            $this->session->set('user_id', $user->id);
            $this->session->set('username', $user->username);
            return true;
        }

        return false;
    }

    // Cerrar sesión
    public function logout()
    {
        $this->session->invalidate();
    }

    // Obtener el usuario autenticado
    public function user()
    {
        if ($this->check()) {
            return [
                'id' => $this->session->get('user_id'),
                'username' => $this->session->get('username')
            ];
        }
        return null;
    }
}
