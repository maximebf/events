<?php

namespace Events\Tests;

use Events\EventProxy,
    Events\EventDispatcher;

class EventProxyTest extends EventsTestCase
{
    public function testProxy()
    {
        $o = new TestClass();
        $d = new EventDispatcher();
        $l = new TestListener();
        $d->addListener($l);

        $p = new EventProxy($o, $d);
        $p->foo = 'bar';
        $p->foo;
        isset($p->foo);
        unset($p->foo);
        $p->foobar();

        $e = $l->getReceivedEvents();
        $this->assertCount(5, $e);
        $this->assertEquals('proxy.set', $e[0]->getName());
        $this->assertEquals($o, $e[0]->getSender());
        $this->assertEquals('proxy.get', $e[1]->getName());
        $this->assertEquals('proxy.isset', $e[2]->getName());
        $this->assertEquals('proxy.unset', $e[3]->getName());
        $this->assertEquals('proxy.call', $e[4]->getName());
    }
}
