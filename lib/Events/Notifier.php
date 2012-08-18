<?php
/*
 * This file is part of the Events package.
 *
 * (c) 2012 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Events;

/**
 * Utility class to ease the notification of events
 */
class Notifier
{
    /** @var EventDispatcher */
    protected $dispatcher;

    /** @var object */
    protected $sender;

    /** @var string */
    protected $prefix = '';

    /** @var string */
    protected $eventClass = 'Events\Event';

    /**
     * @param EventDispatcher $dispatcher
     * @param object $sender
     * @param string $prefix Event name prefix
     * @param string $eventClass Class name of the Event class
     */
    public function __construct(EventDispatcher $dispatcher, $sender = null, $prefix = '', $eventClass = 'Events\Event')
    {
        $this->dispatcher = $dispatcher;
        $this->sender = $sender;
        $this->prefix = $prefix ?: '';
        $this->eventClass = $eventClass;
    }

    /**
     * @param EventDispatcher $dispatcher
     * @return Notifier
     */
    public function setEventDispatcher(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        return $this;
    }

    /**
     * @return EventDispatcher
     */
    public function getEventDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @param object $sender
     * @return Notifier
     */
    public function setSender($sender = null)
    {
        $this->sender = $sender;
        return $this;
    }

    /**
     * @return object
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Sets the event name prefix
     * 
     * @param string $prefix
     * @return Notifier
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string $classname
     * @return Notifier
     */
    public function setEventClass($classname)
    {
        $this->eventClass = $classname;
        return $this;
    }

    /**
     * @return string
     */
    public function getEventClass()
    {
        return $this->eventClass;
    }

    /**
     * Creates an event object
     * 
     * @param string $eventName
     * @param array $params
     * @return Event
     */
    public function createEvent($eventName, array $params = array())
    {
        $class = $this->eventClass;
        return new $class($this->sender, $this->prefix . $eventName, $params);
    }

    /**
     * Creates and dispatches an event
     * 
     * @see EventDispatcher::notify()
     * @param string $eventName
     * @param array $params
     * @return Event
     */
    public function notify($eventName, array $params = array())
    {
        $this->dispatcher->notify($e = $this->createEvent($eventName, $params));
        return $e;
    }

    /**
     * Creates and dispatches an event until it has been processed.
     * 
     * @see EventDispatcher::notifyUntil()
     * @param string $eventName
     * @param array $params
     * @param callback $callback
     * @return Event
     */
    public function notifyUntil($eventName, array $params = array(), $callback = null)
    {
        $this->dispatcher->notifyUntil($e = $this->createEvent($eventName, $params), $callback);
        return $e;
    }

    /**
     * @param string $eventName
     * @param array $params
     * @return Event
     */
    public function __invoke($eventName, array $params = array())
    {
        return $this->notify($eventName, $params);
    }
}
