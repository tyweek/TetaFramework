<?php

namespace App\Controllers;

use TetaFramework\Http\Request;
use TetaFramework\Http\Response;
use TetaFramework\Http\Session;
use TetaFramework\View;
use TetaFramework\Template\Template;

class HomeController extends Controller
{
    public function index()
    {
        $hs = (new Session())->Validate();
        // $content = View::render('home', ['loggedIn' => $hs,'user' => (new Session())->get("user")]);
        $template = new Template();
        $template->assign('lang', $this->getLang()->getAllTranslate());
        $template->assign('locale', $this->getLang()->getLocale());

        $content = $template->render("home", ['loggedIn' => $hs,'user' => (new Session())->get("user")]);
        return new Response($content);
    }
}
