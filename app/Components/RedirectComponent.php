<?php

namespace App\Components;

use Symfony\Component\HttpFoundation\RedirectResponse;

class RedirectComponent
{
    public static function to($url, $status = 302, $headers = [])
    {
        return new RedirectResponse($url, $status, $headers);
    }
}
