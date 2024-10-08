<?php

namespace App\Controllers;

use App\Models\Lista;
use TetaFramework\Http\Response;
use TetaFramework\Http\Request;
use TetaFramework\Http\RedirectResponse;
use TetaFramework\Http\Session;
use TetaFramework\View;

class ListaController extends Controller
{
    public function index(Request $request): Response
    {
        // foreach ($items as $lista) {
        //     echo "ID: " . $lista->id . "<br>";
        //     echo "Name: " . $lista->name . "<br>";
        //     echo "Created At: " . $lista->created_at . "<br>";
        //     echo "Updated At: " . $lista->updated_at . "<br><br>";
        // }
        
        $start = '2024-09-08';
        $end = '2024-10-10';

        $items = (new Lista())->where('name', 'LIKE', '%brando%')->get(Lista::class);
        
        view::assign('items', $items);
        view::assign('lang', $this->getLang()->getAllTranslate());
        view::assign('uri', $request->getPathInfo());
        view::assign('locale', $this->getLang()->getLocale());
        $content =  view::render('Lista');
        return new Response($content);
    }

}
