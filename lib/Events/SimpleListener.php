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
 * Listens to all events
 */
class SimpleListener implements EventListener
{
    /** @var callback */
    protected $callback;

    /**
     * @param callback $callback
     */
    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    /**
     * {@inheritDoc}
     */
    public function match(Event $event)
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(Event $event)
    {
        return call_user_func($this->callback, $event);
    }
}
