<?php

namespace Events\Tests;

use Events\EventListener,
    Events\Event;

class TestListener implements EventListener
{
    protected $receivedEvents = array();

    public function match(Event $e)
    {
        return true;
    }

    public function handle(Event $e)
    {
        $this->receivedEvents[] = $e;
    }

    public function getReceivedEvents()
    {
        return $this->receivedEvents;
    }

    public function getLastReceivedEvent()
    {
        if (count($this->receivedEvents) === 0) {
            return false;
        }
        return $this->receivedEvents[count($this->receivedEvents) - 1];
    }
}
