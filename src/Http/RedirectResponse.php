<?php
namespace TetaFramework\Http;

class RedirectResponse extends Response
{
    public function __construct($url, $statusCode = 302)
    {
        parent::__construct('', $statusCode);
        $this->headers['Location'] = $url;
    }

    public function send()
    {
        http_response_code($this->statusCode);
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }
    }
}
