<?php

namespace Events\Tests;

use Events\Notifier,
    Events\EventDispatcher;

class NotifierTest extends EventsTestCase
{
    public function testCreateEvent()
    {
        $o = new \stdClass();
        $n = new Notifier(new EventDispatcher(), $o);

        $e = $n->createEvent('foo', array('a' => 'a'));
        $this->assertInstanceOf('Events\Event', $e);
        $this->assertEquals($o, $e->getSender());
        $this->assertEquals('foo', $e->getName());
        $this->assertEquals('a', $e->getParam('a'));
    }

    public function testPrefix()
    {
        $n = new Notifier(new EventDispatcher(), null, 'foo.');
        $e = $n->createEvent('bar');
        $this->assertEquals('foo.bar', $e->getName());
    }

    public function testEventClass()
    {
        $n = new Notifier(new EventDispatcher(), null, null, 'Events\Tests\TestEvent');
        $e = $n->createEvent('bar');
        $this->assertEquals('bar', $e->getName());
        $this->assertInstanceOf('Events\Tests\TestEvent', $e);
    }

    public function testNotify()
    {
        $d = new EventDispatcher();
        $l = new TestListener();
        $d->addListener($l);

        $o = new \stdClass();
        $n = new Notifier($d, $o);
        $n->notify('foo');

        $e = $l->getLastReceivedEvent();
        $this->assertEquals($o, $e->getSender());
        $this->assertEquals('foo', $e->getName());
    }

    public function testNotifyUntil()
    {
        $d = new EventDispatcher();

        $o = new \stdClass();
        $n = new Notifier($d, $o);
        $n->notifyUntil('foo');

        $l = new TestListener();
        $d->addListener($l);
        $e = $l->getLastReceivedEvent();
        $this->assertEquals($o, $e->getSender());
        $this->assertEquals('foo', $e->getName());
    }
}
