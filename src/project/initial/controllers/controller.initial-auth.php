<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\AuthSession;
use TetaFramework\Http\RedirectResponse;

use TetaFramework\Http\Session;
use TetaFramework\Http\Request;
use TetaFramework\Http\Response;
use TetaFramework\Template\Template;
use TetaFramework\Validation\Validator;

class AuthController extends Controller
{
    public function index(Request $request) : Response
    {
        $hs = $this->checkSession();
        if($hs)
        {
            return new RedirectResponse('/panel');
        }

        $template = new Template();
        $template->assign('lang', $this->getLang()->getAllTranslate());
        $content = $template->render("login");

        // $content = View::render('login', []);
        return new Response($content);
    }

    public function register(Request $request)
    {
        if($request->getMethod() == "POST")
        {
            $data = $request->all();

            $validator = Validator::make($data, [
                'name' => 'required',
                'email' => 'required|email',
                'age' => 'required|int',
                "salary" => 'required|decimal',
                'password' => 'required',
            ]);

           // En tu controlador
            $template = new Template();
            $template->assign('name', $request->get('name'));
            $template->assign('email', $request->get('email'));
            $template->assign('age', $request->get('age'));
            $template->assign('salary', $request->get('salary'));
            // Realiza la validación de los datos
            $errors = $validator->errors();
            // Asigna los errores a la plantilla
            $template->assign('errors', $errors);
            $content = $template->render("register");
            // En tu controlador
            if ($validator->fails()) {
              return new Response($content);
            }else{
                //register
                $user = [
                    'name' => $request->get('name'),
                    'email' => $request->get('email'),
                    'password' => password_hash($request->get('password'),PASSWORD_DEFAULT)
                ];
                User::create($user);
                return new RedirectResponse("/login");
            }
        }

        if($request->getMethod() == "GET")
        {
            $hs = $this->checkSession();
            if($hs)
            {
                return new RedirectResponse('/panel');
            }

            $template = new Template();
            $content = $template->render("register");
            return new Response($content);

        }
    }

    public function login(Request $request)
    {
        // Obtener los datos del formulario
        $username = $request->get('username');
        $password = $request->get('password');

        $validator = Validator::make($request->all(),
        [
            "username" => "required|email",
            "password" => "required"
        ]);
    
        // Aquí va tu lógica de autenticación para verificar el usuario y contraseña
        $user = User::where('email', $username);
        
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
            $session->set('user', ["user_id" => $user->id,'name' => $user->name,'profile' => ['email' => $user->email,'age' => 15]]);
    
            // Redirigir al usuario a alguna página después del inicio de sesión exitoso
            return new RedirectResponse("/panel");
        } else {
            // Autenticación fallida, redirigir de nuevo al formulario de inicio de sesión con un mensaje de error
            // return new Response('Credenciales incorrectas, vuelve a intentarlo');
            $template = new Template();
            $credentials = [$this->getLang()->translate('credentials_invalid')];
            if($validator->fails())
            {
                $template->assign('errors', $validator->errors());
            }else{
                $template->assign('credentials', $credentials);
                $template->assign('username', $username);
            }
            // Asigna los errores a la plantilla
            $template->assign('lang', $this->getLang()->getAllTranslate());
            $content = $template->render("login");
            return new Response($content);
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
        return new RedirectResponse('/');
    }
    public function toLogin()
    {
        $hs = $this->checkSession();
        if($hs)
        {
            return new RedirectResponse('/panel');
        }
        return new RedirectResponse('/');
    }
}
