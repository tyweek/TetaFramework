<?php

namespace App\Controllers;

use TetaFramework\Http\Session;
use TetaFramework\Http\Auth;
use TetaFramework\Http\RedirectResponse;
use TetaFramework\Language\Language;
use TetaFramework\View;

use function PHPSTORM_META\type;

class Controller
{
    protected $language;
    protected $requireAuth;
    protected $ControllerName = "Controller";
    
    public function __construct(Language $language)
    {
        $this->language = $language;
        view::assign('ControllerName', $this->ControllerName);
    }

    protected function getLang(){
        return $this->language;
    }
    protected function checkSession()
    {
        $auth = new Auth();
        return $auth->check();
    }
}
