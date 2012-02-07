<?php

namespace Events\Tests;

use Events\Event;

class TestClassListener
{
    protected $receivedEvents = array();

    public function onTest(Event $e)
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
