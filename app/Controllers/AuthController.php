<?php

namespace App\Controllers;

use App\Components\RedirectComponent;
use App\Models\User;
use App\Models\AuthSession;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use TetaFramework\View;

class AuthController extends Controller
{
    public function index(Request $request)
    {
        $content = View::render('login', []);
        return new Response($content);
    }

    public function login(Request $request)
    {
        // Obtener los datos del formulario
        $username = $request->get('email');
        $password = $request->get('password');
    
        // Aquí va tu lógica de autenticación para verificar el usuario y contraseña
        $user = User::where('email', $username)->first();
        
        if ($user && password_verify($password, $user->password)) {
            // Autenticación exitosa
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime(' +1 hours '));

            AuthSession::create([
                'user_id' => $user->id,
                'token' => $token,
                'expires_at' => $expiresAt
            ]);
    
            // Inicializar la sesión
            $session = new Session();
            // $session->start();
    
            // Almacenar el token en la sesión del usuario
            $session->set('token', $token);
            $session->set('user_id', $user->id);
    
            // Redirigir al usuario a alguna página después del inicio de sesión exitoso
            return RedirectComponent::to('/');
        } else {
            // Autenticación fallida, redirigir de nuevo al formulario de inicio de sesión con un mensaje de error
            return new Response('Credenciales incorrectas, vuelve a intentarlo');
        }
    }
    
    public function logout()
    {
        $session = new Session();
        // $session->start();
        
        $token = $session->get('token');
        AuthSession::where('token', $token)->delete();

        // Eliminar todos los datos de la sesión
        $session->invalidate();
        $session->clear();

        

        // Redirigir al usuario a la página de inicio o a donde desees después del cierre de sesión
        return RedirectComponent::to('/');
    }
}
