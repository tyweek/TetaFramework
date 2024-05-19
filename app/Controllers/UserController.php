<?php

namespace App\Controllers;

use App\Models\User;
use TetaFramework\Http\RedirectResponse;
use TetaFramework\Template\Template;
// use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\HttpFoundation\Request;

use TetaFramework\Http\Request;
use TetaFramework\Http\Response;
use TetaFramework\Http\Session;

class UserController extends Controller
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

        $content = $template->render("user");
        // $content = $template->render("user",['lang' => $this->getLang()]);
        // View::render('user', ['request' => $httpRequest])
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
