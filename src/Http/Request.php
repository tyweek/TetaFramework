<?php

// src/Http/Request.php
namespace TetaFramework\Http;

class Request
{
    private $query;
    private $request;
    private $server;

    public function __construct(array $query = [], array $request = [], array $server = [])
    {
        $this->query = $query;
        $this->request = $request;
        $this->server = $server;
    }

    public static function createFromGlobals()
    {
        return new self($_GET, $_POST, $_SERVER);
    }

    public function get($key, $default = null)
    {
        if (isset($this->request[$key])) {
            return $this->request[$key];
        }

        if (isset($this->query[$key])) {
            return $this->query[$key];
        }

        return $default;
    }
    public function GetRequest()
    {
        return $this->query;
    }

    public function getMethod()
    {
        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }

    public function getPathInfo()
    {
        $path = $this->server['REQUEST_URI'] ?? '/';
        $path = parse_url($path, PHP_URL_PATH);
        return $path;
    }

    // Otros métodos como getQuery, getPost, etc. pueden ser añadidos según necesidad
}
