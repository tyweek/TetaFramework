<?php

namespace App\Controllers;

// use Symfony\Component\HttpFoundation\Session\Session;
use TetaFramework\Http\Session;
use App\Components\RedirectComponent;
use App\Models\AuthSession;
use TetaFramework\Http\RedirectResponse;
use TetaFramework\Language\Language;

use function PHPSTORM_META\type;

class Controller
{
    protected $language;

    public function __construct(Language $language)
    {
        $this->language = $language;
    }

    protected function getLang(){
        return $this->language;
    }
    protected function checkSession()
    {
        // Iniciar la sesión
        $session = new Session();

        $haveSession = $session->Validate();
        if($haveSession)
        {
            $token = $session->get('token');

            $now =date('Y-m-d H:i:s');
            $sessionRecord = AuthSession::where('token', $token)->first();
            if (!$sessionRecord || $sessionRecord->expires_at < $now) {
                // redireccionamos al logout por sesion invalida
                // return new RedirectResponse("/logout");
                $session = new Session();
                $session->invalidate();
                return false;
            }
            return true;
                // return new RedirectResponse(($redirectPage));
        }else{
            return false;
        }
    }
}
