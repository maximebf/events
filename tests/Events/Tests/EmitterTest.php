<?php

namespace Events\Tests;

class EmitterTest extends EventsTestCase
{
    public function testEmitter()
    {
        $l = new TestListener();
        $e = new TestEmitter();
        $e->addListener($l);
        $e->foobar();
        $this->assertEquals('foobar', $l->getLastReceivedEvent()->getName());
    }
}
