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

/**
 * Proxy all calls and property access to an object and sends events for each actions
 * 
 * Events:
 *  - __get : get
 *  - __set : set
 *  - __isset : isset
 *  - __unset : unset
 *  - __call : call
 *  
 *  (__toString() and __invoke() are also proxied but are notified as "call")
 */
class EventProxy
{
    /** @var object */
    protected $object;

    /** @var Notifier */
    protected $notifier;

    /**
     * @param object $object The object to proxy calls to
     * @param EventDispatcher $dispatcher
     */
    public function __construct($object, EventDispatcher $dispatcher)
    {
        $this->object = $object;
        $this->notifier = new Notifier($dispatcher, $object, 'proxy.');
    }

    public function __get($property)
    {
        $this->notifier->notify('get', array('property' => $property));
        return $this->object->$property;
    }

    public function __set($property, $value)
    {
        $event = $this->notifier->notify('set', array('property' => $property, 'value' => $value));
        $this->object->$property = $event->getReturnValue($value);
    }

    public function __isset($property)
    {
        $this->notifier->notify('isset', array('property' => $property));
        return isset($this->object->$property);
    }

    public function __unset($property)
    {
        $this->notifier->notify('unset', array('property' => $property));
        unset($this->object->$property);
    }

    public function __toString()
    {
        return $this->__call('__toString', func_get_args());
    }

    public function __invoke()
    {
        return $this->__call('__invoke', func_get_args());
    }

    public function __call($method, $args)
    {
        $event = $this->notifier->notify('call', array('method' => $method, 'args' => $args));
        return call_user_func_array(array($this->object, $method), $event->getReturnValue($args));
    }
}
