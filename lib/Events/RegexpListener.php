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
 * Listens to event which name matches the pattern
 */
class RegexpListener implements EventListener
{
    /** @var string */
    protected $pattern;

    /** @var callback */
    protected $callback;

    /**
     * @param string $pattern
     * @param callback $callback
     */
    public function __construct($pattern, $callback)
    {
        $this->pattern = $pattern;
        $this->callback = $callback;
    }

    /**
     * {@inheritDoc}
     */
    public function match(Event $event)
    {
        return preg_match($this->pattern, $event->getName());
    }

    /**
     * {@inheritDoc}
     */
    public function handle(Event $event)
    {
        return call_user_func($this->callback, $event);
    }
}
