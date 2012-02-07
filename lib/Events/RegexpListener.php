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
 * Listens to event which name matches the pattern
 */
class RegexpListener implements EventListener
{
    /** @var string */
    protected $pattern;

    /** @var callback */
    protected $callback;

    /**
     * @param string $pattern
     * @param callback $callback
     */
    public function __construct($pattern, $callback)
    {
        $this->pattern = $pattern;
        $this->callback = $callback;
    }

    /**
     * {@inheritDoc}
     */
    public function match(Event $event)
    {
        return preg_match($this->pattern, $event->getName());
    }

    /**
     * {@inheritDoc}
     */
    public function handle(Event $event)
    {
        return call_user_func($this->callback, $event);
    }
}
