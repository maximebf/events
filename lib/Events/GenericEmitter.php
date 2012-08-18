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
 * Generic event emitter
 */
abstract class GenericEmitter implements EventEmitter
{
    /** @var EventDispatcher */
    protected $eventDispatcher;

    /** @var Notifier */
    protected $eventNotifier;

    /** @var string */
    protected $eventPrefix = '';

    /** @var string */
    protected $eventClass = 'Events\Event';

    public function __construct()
    {
        $this->eventDispatcher = new EventDispatcher();
        $this->eventNotifier = new Notifier($this->eventDispatcher, $this, $this->eventPrefix, $this->eventClass);
    }

    /**
     * @see EventDispatcher::on()
     * @param string $pattern Either a regexp, a callback, an object, an event name or an array
     * @param function $callback Needed if $pattern is a regexp or an event name
     * @param integer $priority Listener's priority, higher is more important
     * @param boolean $important Whether this listener is more important than other ones of the same priority
     * @return EventDispatcher
     */
    public function on($pattern, $callback = null, $priority = 0, $important = false)
    {
        $this->eventDispatcher->on($pattern, $callback, $priority, $important);
        return $this;     
    }

    /**
     * {@inheritDoc}
     */
    public function addListener(EventListener $listener, $priority = 0, $important = false)
    {
        $this->eventDispatcher->addListener($listener, $priority, $important);
        return $this;        
    }

    /**
     * {@inheritDoc}
     */
    public function removeListener(EventListener $listener)
    {
        $this->eventDispatcher->removeListener($listener);
        return $this;  
    }

    /**
     * @see Notifier::notify()
     * @param string $eventName
     * @param array $params
     * @return bool
     */
    protected function notify($eventName, array $params = array())
    {
        return $this->eventNotifier->notify($eventName, $params);
    }

    /**
     * @see Notifier::notifyUntil()
     * @param string $eventName
     * @param array $params
     * @param callback $callback
     * @return bool
     */
    protected function notifyUntil($eventName, array $params = array(), $callback = null)
    {
        return $this->eventNotifier->notifyUntil($eventName, $params, $callback);
    }
}
