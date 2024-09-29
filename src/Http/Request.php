<?php

// src/Http/Request.php
namespace TetaFramework\Http;

class Request
{
    private $query;
    private $request;
    private $server;
    private $files;

    public function __construct(array $query = [], array $request = [], array $server = [], array $files = [])
    {
        $this->query = $query;
        $this->request = $request;
        $this->server = $server;
        $this->files = $files;
    }

    public static function createFromGlobals()
    {
        return new self($_GET, $_POST, $_SERVER, $_FILES);
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

    public function all()
    {
        // Combina los datos de query y request
        return array_merge($this->query, $this->request);
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getServer()
    {
        return $this->server;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function getFile($key)
    {
        return $this->files[$key] ?? null;
    }

    // Otros métodos como getQuery, getPost, etc. pueden ser añadidos según necesidad
}
