<?php

namespace Events\Tests;

class TestClass
{
    public $calls = array();

    public function __call($method, $args)
    {
        $this->calls[] = array($method, $args);
    }
}
