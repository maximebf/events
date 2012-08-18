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
 * Objects which emit events must implement this interface
 */
interface EventEmitter
{
    /**
     * @param EventListener $listener
     * @param integer $priority The higher, the more priority
     * @param boolean $important Whether this event is more important than other event of the same priority
     */
    function addListener(EventListener $listener, $priority = 0, $important = false);

    /**
     * @param EventListener $listener
     */
    function removeListener(EventListener $listener);
}
