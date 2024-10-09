<?php

// src/Http/Session.php
namespace TetaFramework\Http;

class Session
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function remove($key)
    {
        unset($_SESSION[$key]);
    }

    public function has($key)
    {
        return isset($_SESSION[$key]);
    }

    public function clear()
    {
        session_unset();
    }

    public function destroy()
    {
        session_destroy();
    }

    public function getId()
    {
        return session_id();
    }

    public function regenerateId($deleteOldSession = true)
    {
        session_regenerate_id($deleteOldSession);
    }

    public function invalidate($regenerate = true)
    {
        $this->clear();
        $this->destroy();
        
        if ($regenerate) {
            session_start();
            $this->regenerateId(true);
        }
    }
    public function Validate($custom = null,$primary = "token")
    {
        $k = $custom;
        if($k == NULL)
            $k = $primary;
        return $this->has($k);
    }

    public function PrintAll()
    {
        return print_r($_SESSION);
    }
}
