<?php

namespace Events\Tests;

use Events\EventDispatcher,
    Events\Event;

class EventDispatcherTest extends EventsTestCase
{
    public function testAddListener()
    {
        $dispatcher = new EventDispatcher();
        $listener = new TestListener();
        $dispatcher->addListener($listener);

        $this->assertContains($listener, $dispatcher->getListeners());
        $this->assertTrue($dispatcher->hasListener($listener));
    }

    public function testRemoveListener()
    {
        $dispatcher = new EventDispatcher();
        $listener = new TestListener();
        $dispatcher->addListener($listener);
        $dispatcher->removeListener($listener);
        $this->assertEmpty($dispatcher->getListeners());
        $this->assertFalse($dispatcher->hasListener($listener));
        $dispatcher->addListener($listener);
        $dispatcher->removeAllListeners();
        $this->assertEmpty($dispatcher->getListeners());
    }

    public function testNotify()
    {
        $dispatcher = new EventDispatcher();
        $listener = new TestListener();
        $event = new Event(null, 'test');

        $dispatcher->addListener($listener);
        $processed = $dispatcher->notify($event);

        $this->assertTrue($processed);
        $this->assertEquals($event, $listener->getLastReceivedEvent());
    }

    public function testNotifyUntil()
    {
        $dispatcher = new EventDispatcher();
        $listener = new TestListener();
        $event = new Event(null, 'test');

        $processed = $dispatcher->notifyUntil($event);
        $this->assertFalse($processed);

        $dispatcher->addListener($listener);
        $this->assertEquals($event, $listener->getLastReceivedEvent());
    }

    public function testNotifyWithOnSimpleListener()
    {
        $dispatcher = new EventDispatcher();
        $self = $this;
        $event = new Event(null, 'test');
        $dispatcher->on(function($e) use ($self, $event) {
            $self->assertEquals($event, $e);
        });
        $dispatcher->notify($event);
    }

    public function testNotifyWithOnNameListener()
    {
        $dispatcher = new EventDispatcher();
        $self = $this;
        $event = new Event(null, 'test');
        $dispatcher->on('test', function($e) use ($self, $event) {
            $self->assertEquals($event, $e);
        });
        $dispatcher->notify($event);
    }

    public function testNotifyWithOnWildcardListener()
    {
        $dispatcher = new EventDispatcher();
        $self = $this;
        $event = new Event(null, 'foo.bar');
        $dispatcher->on('foo.*', function($e) use ($self, $event) {
            $self->assertEquals($event, $e);
        });
        $dispatcher->notify($event);
    }

    public function testNotifyWithOnRegexpListener()
    {
        $dispatcher = new EventDispatcher();
        $self = $this;
        $event = new Event(null, 'foo.bar');
        $dispatcher->on('/^foo\.(.+)$/', function($e) use ($self, $event) {
            $self->assertEquals($event, $e);
        });
        $dispatcher->notify($event);
    }

    public function testNotifyWithOnClassListener()
    {
        $dispatcher = new EventDispatcher();
        $listener = new TestClassListener();
        $event = new Event(null, 'test');
        $dispatcher->on($listener);
        $dispatcher->notify($event);
        $this->assertEquals($event, $listener->getLastReceivedEvent());
    }

    public function testEventDispatcherAsListener()
    {
        $dispatcherListener = new EventDispatcher();
        $listener = new TestListener();
        $dispatcherListener->addListener($listener);

        $dispatcher = new EventDispatcher();
        $dispatcher->addListener($dispatcherListener);
        $event = new Event(null, 'test');
        $dispatcher->notify($event);

        $this->assertEquals($event, $listener->getLastReceivedEvent());
    }
}
