<?php

namespace Intersect\Core\Http;

class Response {

    private $body;
    private $status;

    public function __construct($body, $status = 200)
    {
        $this->body = $body;
        $this->status = (int) $status;

        http_response_code($status);
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getStatus()
    {
        return $this->status;
    }

}