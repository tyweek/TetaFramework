<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Components\RedirectComponent;
use App\Models\AuthSession;
use DateTime;

use function PHPSTORM_META\type;

class Controller
{
    protected function checkSession()
    {
        // Iniciar la sesión
        $session = new Session();
        $session->start();

        $token = $session->get('token');
        // Comprobar si hay un usuario autenticado en la sesión
        if (!$session->has('token')) {
            // Si no hay un usuario autenticado, redirigir al usuario a la página de inicio de sesión
            return RedirectComponent::to('/login');
        }

        $now =date('Y-m-d H:i:s');
        $sessionRecord = AuthSession::where('token', $token)->first();
        if (!$sessionRecord || $sessionRecord->expires_at < $now) {
            // Si no hay una sesión válida, redirigir al usuario a la página de inicio de sesión
            return RedirectComponent::to("/logout");
        }
        // Si hay un usuario autenticado, permitir que continúe la ejecución
    }
}
