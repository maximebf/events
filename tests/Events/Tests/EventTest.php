<?php

namespace Events\Tests;

use Events\Event;

class EventTest extends EventsTestCase
{
    public function testSenderAndName()
    {
        $e = new Event(null, 'test');
        $this->assertNull($e->getSender());
        $this->assertEquals('test', $e->getName());

        $o = new \stdClass();
        $e = new Event($o, 'test');
        $this->assertEquals($o, $e->getSender());
    }

    public function testParameters()
    {
        $params = array('a' => 'a', 'b' => 'b');
        $e = new Event(null, 'test', $params);

        $this->assertEquals('a', $e->getParam('a'));
        $this->assertEquals('a', $e->a);
        $this->assertEquals('b', $e->getParam('b'));
        $this->assertEquals('b', $e->b);
        $this->assertNull($e->getParam('c'));
        $this->assertEquals('c', $e->getParam('c', 'c'));

        $this->assertTrue($e->hasParam('a'));
        $this->assertTrue($e->hasParam('b'));
        $this->assertFalse($e->hasParam('c'));

        $this->assertEquals($params, $e->getParams());
    }

    public function testReturnValue()
    {
        $e = new Event(null, 'test');

        $this->assertNull($e->getReturnValue());
        $this->assertEquals('default', $e->getReturnValue('default'));

        $e->setReturnValue('foo');
        $this->assertEquals('foo', $e->getReturnValue());
        $this->assertEquals('foo', $e->getReturnValue('default'));
        
        $e->setReturnValue(null);
        $this->assertNull($e->getReturnValue());
        $this->assertNull($e->getReturnValue('default'));
        
        $e->clearReturnValue();
        $this->assertNull($e->getReturnValue());
        $this->assertEquals('default', $e->getReturnValue('default'));
    }

    public function testCancel()
    {
        $e = new Event(null, 'test');
        $this->assertFalse($e->isCancelled());
        $e->cancel();
        $this->assertTrue($e->isCancelled());
    }

    public function testStopPropagation()
    {
        $e = new Event(null, 'test');
        $this->assertFalse($e->isPropagationStopped());
        $e->stopPropagation();
        $this->assertTrue($e->isPropagationStopped());
    }
}
