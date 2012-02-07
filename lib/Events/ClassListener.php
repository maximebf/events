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
 * Event handlers must be implemented as method of a class.
 * 
 * The method's name should start with "on" and followed by
 * the camel cased event name (the following characters will be removed: _ . - :)
 * 
 * If the method call returns something other than null, it will be used
 * as the event return value.
 */
class ClassListener implements EventListener
{
    /** @var object */
    protected $object;

    /** @var bool */
    protected $paramsAsArgs;

    /**
     * @param object $object The object on which the methods are located ($this by default)
     * @param bool $paramsAsArgs Whether event params should be used as method args
     */
    public function __construct($object = null, $paramsAsArgs = false)
    {
        $this->object = $object ?: $this;
        $this->paramsAsArgs = $paramsAsArgs;
    }

    /**
     * Returns the method name associated to an event
     * 
     * @param Event $event
     * @return string
     */
    protected function getMethodName(Event $event)
    {
        return 'on' . str_replace(' ', '', ucwords(
            str_replace(array('_', '.', '-', ':'), ' ', $event->getName())));
    }

    /**
     * {@inheritDoc}
     */
    public function match(Event $event)
    {
        return method_exists($this->object, $this->getMethodName($event));
    }

    /**
     * {@inheritDoc}
     */
    public function handle(Event $event)
    {
        $args = array();
        $method = new \ReflectionMethod($this->object, $this->getMethodName($event));
        if ($this->paramsAsArgs) {
            foreach ($method->getParameters() as $param) {
                if ($event->hasParam($param->getName())) {
                    $args[] = $event->getParam($param->getName());
                } else if (!$param->isOptional()) {
                    throw new EventException("Missing parameter '" . $param->getName() 
                        . "' for '" . get_class($this->object) . "::" . $method->getName() . "'");
                } else {
                    $args[] = $param->getDefaultValue();
                }
            }
        }
        $args[] = $event;
        $return = $method->invokeArgs($this->object, $args);
        if ($return !== null) {
            $event->setReturnValue($return);
        }
    }
}
