<?php

namespace Tequilarapido\PythonBridge;

class Response
{
    /** @var string */
    protected $output;

    public function __construct($output)
    {
        $this->output = rtrim($output);
    }

    public function output()
    {
        return $this->output;
    }

    public function json()
    {
        return json_decode($this->output, JSON_OBJECT_AS_ARRAY);
    }

}
