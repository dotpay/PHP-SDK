<?php
/**
 * Copyright (c) 2021 PayPro S.A. <tech@dotpay.pl>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @author    Dotpay Team <tech@dotpay.pl>
 * @copyright PayPro S.A.
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */

namespace Dotpay\Action;

/**
 * Action provides a functionality of some actions, executed in concrete times thanks to special interface.
 */
abstract class Action
{
    /**
     * @var callable A callable value to executed
     */
    protected $userFunc;

    /**
     * @var mixed If a callable value contains a function which needs only one argument, it can be passed from this value
     */
    protected $oneArgument;

    /**
     * Initialize an action object.
     *
     * @param callable $userFunc Initial callable value, which can be executed in a coorect time
     */
    public function __construct(callable $userFunc = null)
    {
        if ($userFunc !== null) {
            $this->setUserFunc($userFunc);
        }
    }

    /**
     * Return a callable value which is set.
     *
     * @return callable
     */
    public function getUserFunc()
    {
        return $this->userFunc;
    }

    /**
     * Return one argument, which is set.
     *
     * @return mixed
     */
    public function getOneArgument()
    {
        return $this->oneArgument;
    }

    /**
     * Set a callable value which will be executed.
     *
     * @param callable $userFunc Callable value
     *
     * @return Action
     */
    public function setUserFunc(callable $userFunc)
    {
        $this->userFunc = $userFunc;

        return $this;
    }

    /**
     * Set an one argument which will be passed to the function during its execution.
     *
     * @param mixed $oneArgument Argument to passing to the function during execution
     *
     * @return Action
     */
    public function setOneArgument($oneArgument)
    {
        $this->oneArgument = $oneArgument;

        return $this;
    }

    /**
     * Execute a callback and returns a result of it. It returns null if callback wasn't set.
     *
     * @return mixed
     */
    public function execute()
    {
        $func = $this->userFunc;
        if (!is_callable($func)) {
            return null;
        }
        if ($this->oneArgument !== null) {
            return $func($this->getOneArgument());
        } else {
            return $func();
        }
    }
}
