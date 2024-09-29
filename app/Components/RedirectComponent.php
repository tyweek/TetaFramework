<?php

namespace App\Components;

use TetaFramework\Http\RedirectResponse;

class RedirectComponent
{
    public static function to($url, $status = 302, $headers = [])
    {
        return new RedirectResponse($url, $status, $headers);
    }
}
