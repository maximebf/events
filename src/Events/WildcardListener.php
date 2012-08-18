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
class WildcardListener extends RegexpListener
{
    /**
     * @param string $pattern
     * @param callback $callback
     */
    public function __construct($pattern, $callback)
    {
        $pattern = '/^' . str_replace('\*', '(.+)', preg_quote($pattern)) . '$/';
        parent::__construct($pattern, $callback);
    }
}
