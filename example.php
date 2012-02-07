<?php

include __DIR__ . '/tests/bootstrap.php';

use Events\EventDispatcher,
    Events\Notifier,
    Events\Event;

class Car
{
    protected $notifier;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->notifier = new Notifier($dispatcher, $this, 'car.');
    }

    public function forward($time = 1)
    {
        $this->notifier->notify('forward', array('time' => $time));
    }

    public function turnLeft($degree = 90)
    {
        $this->notifier->notify('turn.left', array('degree' => $degree));
    }

    public function turnRight($degree = 90)
    {
        $this->notifier->notify('turn.right', array('degree' => $degree));
    }
}

$dispatcher = new EventDispatcher();

$dispatcher->on('car.forward', function($e) {
    echo "The car went forward for {$e->time} sec\n";
});

$dispatcher->on('car.turn.left', function($e) {
    echo "The car turned left of {$e->degree} degrees\n";
});

$dispatcher->on('car.turn.right', function($e) {
    echo "The car turned right of {$e->degree} degrees\n";
});

$dispatcher->on('/^car\.turn\.(left|right)$/', function($e) {
    echo "The car turned\n";
});

$dispatcher->on('car.*', function($e) {
    echo "Something happened to the car!\n";
});

$car = new Car($dispatcher);
$car->forward(2);
$car->turnRight();
$car->forward(1);
$car->turnLeft(45);
$car->forward(2);
$car->turnLeft(45);
$car->forward(10);
