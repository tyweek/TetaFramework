<?php

namespace App\Controllers;

use App\Models\User;
use TetaFramework\Http\RedirectResponse;
use TetaFramework\Template\Template;
use TetaFramework\Http\Request;
use TetaFramework\Http\Response;
use TetaFramework\Http\Session;

class PanelController extends Controller
{
    public function index(Request $request)
    {
        $hs = $this->checkSession();
        if(!$hs)
        {
            return new RedirectResponse('/login');
        }
        
        $user = (new Session())->get('user');

        $httpRequest = $request->GetRequest();

        $template = new Template();
        $template->assign('user', $user);
        $template->assign('request', $httpRequest);
        $template->assign('persona', "brando@g.com");
        $template->assign('lang', $this->getLang()->getAllTranslate());

        $content = $template->render("panel");
        // $content = $template->render("user",['lang' => $this->getLang()]);
        // View::render('user', ['request' => $httpRequest])
        return new Response($content);
    }
}
