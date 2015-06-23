<?php

namespace Invoice\Transport;

class Result
{
    private $code;

    private $body;

    public function __construct($code, $body)
    {
        $this->code = $code;
        $this->body = $body;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getBody()
    {
        return $this->body;
    }
}
