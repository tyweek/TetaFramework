<?php
// Response.php
// src/Http/Response.php
namespace TetaFramework\Http;

class Response
{
    protected $content;
    protected $statusCode;
    protected $headers = [];

    public function __construct($content = '', $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }

    public function send()
    {
        http_response_code($this->statusCode);
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }
        echo $this->content;
    }
}
