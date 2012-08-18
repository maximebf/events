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
 * Event listener
 */
interface EventListener
{
    /**
     * Checks if an event can be handled by the listener
     * 
     * @param Event $event
     * @return bool
     */
    function match(Event $event);

    /**
     * Handles the event
     * 
     * @param Event $event
     */
    function handle(Event $event);
}
