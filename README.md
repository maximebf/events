
# Events

Events library for PHP 5.3+

[![Build Status](https://secure.travis-ci.org/maximebf/events.png)](http://travis-ci.org/maximebf/events)

Events provide an event dispatcher with various ways of listening to events.

    $dispatcher = new Events\EventDispatcher();

    $dispatcher->on('car.forward', function($e) {
        echo "The car is goind forward";
    });

    $dispatcher->notify(new Events\Event(null, 'car.forward'));

Check out [example.php](https://github.com/maximebf/events/blob/master/example.php) for a complete example.

## Installation

The easiest way to install Events is using [Composer](https://github.com/composer/composer)
with the following requirement:

    {
        "require": {
            "maximebf/events": ">=0.1.0"
        }
    }

Alternatively, you can [download the archive](https://github.com/maximebf/events/zipball/master) 
and add the lib/ folder to PHP's include path:

    set_include_path('/path/to/lib' . PATH_SEPARATOR . get_include_path());

Events does not provide an autoloader but follows the [PSR-0 convention](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md).  
You can use the following snippet to autoload Events classes:

    spl_autoload_register(function($className) {
        if (substr($className, 0, 6) === 'Events') {
            $filename = str_replace('\\', DIRECTORY_SEPARATOR, trim($className, '\\')) . '.php';
            require_once $filename;
        }
    });

## Listening to events

Events are notified through the `Events\EventDispatcher` class. The easiest way to register
listeners to handle events is using the `on()` method:

 - `on($event_name, $callback)`: listens to event with the specified name, the wildcard (`*`) character can be used
 - `on($regexp, $callback)`: listens to event which name matches the regexp
 - `on($callback)`: listens to all events
 - `on($classname)`: listens using a class an "on" methods (see below)

`$callback` can be any PHP callable (ie. a callback - string or array - or a closure). It will
receive an `Events\Event` object as its only argument.

    $dispatcher->on('car.forward', function($e) {});
    $dispatcher->on('car.*', function($e) {});
    $dispatcher->on('/^car\.(.+)$/', function($e) {});
    $dispatcher->on(function($e) {});

When using `on($classname)` the class should have methods named with the camelized event
name prefixed witn "on". eg: the method `onCarForward()` would listen to event named
car.forward (or car_forward or car-forward).

    class CarListener {
        public function onCarForward($e) {}
    }

    $dispatcher->on('CarListener');

Multiple listeners can listen to the same event. However, some of them may be more
important than others. This can be specified using an integer as the third parameter 
to the `on()` method. The higher, the more important.

    $dispatcher->on('car.forward', function($e) {}, 100);

Events are made of a sender, a name and some parameters.
The event object has the following methods:

 - `getSender()`: returns the object that emitted the event
 - `getName()`: returns the event's name
 - `getParam($name, $default=null)`: returns the $name parameter
 - `getParams()`

For some events a return value may be needed. Listeners can specify a return value 
using `setReturnValue($value)`.

Some events may allow the action they represent to be cancelled. This can be perform
using `cancel()`.

Finally, if multiple listeners handle an event, on of them can stop the event from
propagating to the other one using `stopPropagation()`.

    $dispatcher->on('method_call', function($e) {
        if ($e->getParam('method_name') === 'foobar') {
            $e->cancel();
        } else {
            $e->setReturnValue(true);
        }
    });

Under the hood, when the `on()` method is called, the event dispatcher creates a
listener object of type `Events\EventListener`. You can create custom listener
objects and add them using `addListener()`.

## Dispatching events

Events can be dispatched using the `notify($event)` method of the event dispatcher.
The event must be an object of type `Events\Event`.

    $dispatcher->notify(new Events\Event($sender, $name, $params));

`notify()` returns a boolean indicating if the event has been processed by one or more
listeners.

If you want to ensure that your event is delivered to at leadt one listener, you can
use `notifyUntil($event, $callback=null)`. This method will try to deliver the event
and if that fails, will try with any listeners added in the future. `$callback` will
be called once the event has been processed.

    $dispatcher->notifyUntil(new Events\Event(null, 'foo'), function() {
        echo "Event processed!";
    });

To ease the process of notifying events, an `Events\Notifier` object can be used to
create and dispatch events. Its constructor takes an event dispatcher and the sender
object as mandatory arguments. You can optionaly provide a prefix for event names 
and specify a different event class to use. `notify()` and `notifyUntil()` are both
available but under a different form. Instead of taking an event object, they take
the name of the event and optionaly and array of params.

    $notifier = new Events\Notifier($eventDispatcher, $sender);
    $notifier->notify('foobar', array('param1' => 'value'));

## Other utilities

You can use `Events\GenericEmitter` to create objects which emits event and on which
listeners can be registered.

    class Car extends Events\GenericEmitter {
        protected $eventPrefix = 'car.';
        public function forward() {
            $this->notify('forward');
        }
    }

    $car = new Car();
    $car->on('car.forward', function($e) {});

Events emitter can act as relay. Thus if you have a global event dispatcher, events
of a specific emitter could be relayed by adding the later as a listener to the former:

    $car = new Car();
    $dispatcher = new Events\EventDispatcher();

    $car->on('car.forward', function($e) {});
    $dispatcher->on('car.forward', function($e) {});

    $car->addListener($dispatcher);

The `Events\EventProxy` class can be used as proxy to any objects and will emit events
whenever a property is accessed or a method is called.

    $object = new Events\EventProxy(new MyClass(), $dispatcher);
    $dispatcher->on('proxy.call', function($e) {
        if ($e->getParam('method') === 'foobar') {
            echo "Foobar was called!";
        }
    });
    $object->foobar();

You can create custom event listeners by implementing the `Events\EventListener` interface.

    class CustomListener implements Events\EventListener {
        public function match(Event $e) {
            // checks if the event can be handled by this listener
            return $e->getName() === 'foobar';
        }
        public function handle(Event $e) {
            // do something with $e when match() returned true
            echo $e->getName();
        }
    }

    $dispatcher->addListener(new CustomListener());
