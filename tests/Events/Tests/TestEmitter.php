<?php

namespace Events\Tests;

use Events\GenericEmitter;

class TestEmitter extends GenericEmitter
{
    public function foobar()
    {
        $this->notify('foobar');
    }
}
