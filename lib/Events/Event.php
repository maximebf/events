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

class Event
{
    /** @var object */
    protected $sender;

    /** @var string */
    protected $name;

    /** @var array */
    protected $params = array();

    /** @var mixed */
    protected $returnValue;

    /** @var bool */
    protected $hasReturnValue = false;

    /** @var bool */
    protected $cancelled = false;

    /** @var bool */
    protected $propagationStopped = false;

    /**
     * @param object $sender
     * @param string $name
     * @param array $params
     */
    public function __construct($sender, $name, array $params = array())
    {
        $this->sender = $sender;
        $this->name = $name;
        $this->params = $params;
    }

    /**
     * Returns the object which created the event
     * 
     * @return object
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Returns the event's name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns a parameter
     * 
     * @param string $name
     * @param mixed $default Default value if the parameter does not exist
     * @return mixed
     */
    public function getParam($name, $default = null)
    {
        if (array_key_exists($name, $this->params)) {
            return $this->params[$name];
        }
        return $default;
    }

    /**
     * Checks if a parameter exists
     * 
     * @param string $name
     * @return boolean
     */
    public function hasParam($name)
    {
        return array_key_exists($name, $this->params);
    }

    /**
     * Returns all parameters
     * 
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Sets the return value
     * 
     * @param mixed $value
     * @return Event
     */
    public function setReturnValue($value)
    {
        $this->returnValue = $value;
        $this->hasReturnValue = true;
        return $this;
    }

    /**
     * Clears the return value
     * 
     * @return Event
     */
    public function clearReturnValue()
    {
        $this->returnValue = null;
        $this->hasReturnValue = false;
        return $this;
    }

    /**
     * Returns the value defined using {@see setReturnValue()}
     * 
     * @param mixed $default Default value if setReturnValue() has not been called
     * @return mixed
     */
    public function getReturnValue($default = null)
    {
        if ($this->hasReturnValue) {
            return $this->returnValue;
        }
        return $default;
    }

    /**
     * Checls if a return value has been specified
     * 
     * @return boolean
     */
    public function hasReturnValue()
    {
        return $this->hasReturnValue;
    }

    /**
     * Sets the event has cancelled
     * 
     * @param bool $cancelled
     * @return Event
     */
    public function cancel($cancelled = true)
    {
        $this->cancelled = $cancelled;
        return $this;
    }

    /**
     * Checks if the event has been marked as cancelled
     * 
     * @return boolean
     */
    public function isCancelled()
    {
        return $this->cancelled;
    }

    /**
     * Stops the propagation to other listeners
     * 
     * @return Event
     */
    public function stopPropagation()
    {
        $this->propagationStopped = true;
        return $this;
    }

    /**
     * Checks if the propagation of the event has been stopped
     * 
     * @return boolean
     */
    public function isPropagationStopped()
    {
        return $this->propagationStopped;
    }

    public function __get($property)
    {
        return $this->params[$property];
    }

    public function __isset($property)
    {
        return $this->hasParam($property);
    }

    public function __toString()
    {
        return $this->name;
    }
}
