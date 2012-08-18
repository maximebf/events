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
 * Listens to events with the specified name
 */
class NameListener implements EventListener
{
    /** @var string */
    protected $eventName;

    /** @var callback */
    protected $callback;

    /**
     * @param string $eventName
     * @param callback $callback
     */
    public function __construct($eventName, $callback)
    {
        $this->eventName = $eventName;
        $this->callback = $callback;
    }

    /**
     * {@inheritDoc}
     */
    public function match(Event $event)
    {
        return $this->eventName === $event->getName();
    }

    /**
     * {@inheritDoc}
     */
    public function handle(Event $event)
    {
        return call_user_func($this->callback, $event);
    }
}
