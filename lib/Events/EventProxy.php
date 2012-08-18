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
 * Proxy all calls and property access to an object and sends events for each actions
 * 
 * Events:
 *  - __get : get
 *  - __set : set
 *  - __isset : isset
 *  - __unset : unset
 *  - __call : call
 *  
 *  (__toString() and __invoke() are also proxied but are notified as "call")
 */
class EventProxy
{
    /** @var object */
    protected $object;

    /** @var Notifier */
    protected $notifier;

    /**
     * @param object $object The object to proxy calls to
     * @param EventDispatcher $dispatcher
     */
    public function __construct($object, EventDispatcher $dispatcher)
    {
        $this->object = $object;
        $this->notifier = new Notifier($dispatcher, $object, 'proxy.');
    }

    public function __get($property)
    {
        $this->notifier->notify('get', array('property' => $property));
        return $this->object->$property;
    }

    public function __set($property, $value)
    {
        $event = $this->notifier->notify('set', array('property' => $property, 'value' => $value));
        $this->object->$property = $event->getReturnValue($value);
    }

    public function __isset($property)
    {
        $this->notifier->notify('isset', array('property' => $property));
        return isset($this->object->$property);
    }

    public function __unset($property)
    {
        $this->notifier->notify('unset', array('property' => $property));
        unset($this->object->$property);
    }

    public function __toString()
    {
        return $this->__call('__toString', func_get_args());
    }

    public function __invoke()
    {
        return $this->__call('__invoke', func_get_args());
    }

    public function __call($method, $args)
    {
        $event = $this->notifier->notify('call', array('method' => $method, 'args' => $args));
        return call_user_func_array(array($this->object, $method), $event->getReturnValue($args));
    }
}
