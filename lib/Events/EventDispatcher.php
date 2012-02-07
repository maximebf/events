<?php
/**
 * Events
 * Copyright (c) Maxime Bouroumeau-Fuseau
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author Maxime Bouroumeau-Fuseau
 * @copyright (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 */

namespace Events;

class EventDispatcher implements EventEmitter, EventListener
{
    /** @var array */
    protected $listeners = array();

    /** @var array */
    protected $stackedEvents = array();

    /**
     * @param array $listeners
     */
    public function __construct(array $listeners = array())
    {
        $this->on($listeners);
    }

    /**
     * Creates and adds an event listener depending of the type of $pattern and $callback
     *  
     * @param string $pattern Either a regexp, a callback, an object, an event name or an array
     * @param function $callback Needed if $pattern is a regexp or an event name
     * @param integer $priority Listener's priority, higher is more important
     * @param boolean $important Whether this listener is more important than other ones of the same priority
     * @return EventDispatcher
     */
    public function on($pattern, $callback = null, $priority = 0, $important = false)
    {
        if ($pattern instanceof EventListener) {
            $listener = $pattern;
        } else if (is_string($pattern) && preg_match('#/.+/[a-zA-Z]*#', $pattern)) {
            $listener = new RegexpListener($pattern, $callback);
        } else if (is_callable($pattern) && $callback === null) {
            $listener = new SimpleListener($pattern);
        } else if (is_object($pattern) && $callback === null) {
            $listener = new ClassListener($pattern);
        } else if (is_string($pattern) && $callback !== null) {
            if (strpos($pattern, '*') !== false) {
                $listener = new WildcardListener($pattern, $callback);
            } else {
                $listener = new NameListener($pattern, $callback);
            }
        } else if (is_array($pattern) && $callback === null) {
            foreach ($pattern as $p => $cb) {
                if (is_numeric($p)) {
                    $p = $cb;
                    $cb = null;
                }
                $this->on($p, $cb, $priority, $important);
            }
            return $this;
        } else {
            throw new EventException("No listeners can be created using the specified parameters");
        }
        return $this->addListener($listener, $priority, $important);
    }

    /**
     * Adds a new listener
     * 
     * @param EventListener $listener
     * @param integer $priority Listener's priority, higher is more important
     * @param boolean $important Whether this listener is more important than other ones of the same priority
     */
    public function addListener(EventListener $listener, $priority = 0, $important = false)
    {
        if ($listener === $this) {
            throw new EventException("Adding self as listener in 'Events\EventDispatcher' would cause an infinite loop");
        }
        $priority = -$priority;
        while (isset($this->listeners[$priority])) {
            $priority += $important ? -1 : 1;
        }
        $this->listeners[$priority] = $listener;
        ksort($this->listeners);
        $this->processStackedEvents();
        return $this;
    }

    /**
     * Adds multiple listeners at once
     * 
     * @param array $listeners An array of EventListener objects
     * @return EventDispatcher
     */
    public function addListeners(array $listeners)
    {
        array_map(array($this, 'addListener'), $listeners);
        return $this;
    }

    /**
     * Removes a listener
     * 
     * @param EventListener $listener
     * @return EventDispatcher
     */
    public function removeListener(EventListener $listener)
    {
        if (($i = array_search($listener, $this->listeners)) !== false) {
            unset($this->listeners[$i]);
        }
        return $this;
    }

    /**
     * Removes all listeners
     * 
     * @return EventDispatcher
     */
    public function removeAllListeners()
    {
        $this->listeners = array();
        return $this;
    }

    /**
     * Checks if $listener has been added
     * 
     * @param EventListener $listener
     * @return boolean
     */
    public function hasListener(EventListener $listener)
    {
        foreach ($this->listeners as $l) {
            if ($l === $listener) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns all added listeners
     * 
     * @return array Array of EventListener objects
     */
    public function getListeners()
    {
        return $this->listeners;
    }

    /**
     * Notifies the listeners of an event
     * 
     * @param Event $event
     * @return boolean Whether a listener handled the event
     */
    public function notify(Event $event)
    {
        $processed = false;
        foreach ($this->listeners as $listener) {
            if ($listener->match($event)) {
                $listener->handle($event);
                $processed = true;
                if ($event->isPropagationStopped()) {
                    break;
                }
            }
        }
        return $processed;
    }

    /**
     * Notifies the listeners of an event. Won't stop until the event has been handled by a listener.
     * 
     * @param Event $event
     * @param callback $callback Will be called once the event has been processed
     * @return bool
     */
    public function notifyUntil(Event $event, $callback = null)
    {
        if ($this->notify($event)) {
            if ($callback) {
                $callback();
            }
            return true;
        }
        $this->stackedEvents[] = array($event, $callback);
        return false;
    }

    /**
     * Processes events that were notified using {@see notifyUntil()} but havn't been processed yet
     */
    protected function processStackedEvents()
    {
        $i = 0;
        while ($i < count($this->stackedEvents)) {
            list($event, $callback) = $this->stackedEvents[$i];
            if ($this->notify($event)) {
                if ($callback) {
                    $callback();
                }
                unset($this->stackedEvents[$i]);
                continue;
            }
            $i++;
        }
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
        $this->notify($event);
    }

    public function __invoke(Event $event)
    {
        $this->notify($event);
    }
}
