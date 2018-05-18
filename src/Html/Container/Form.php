<?php
/**
 * Copyright (c) 2018 Dotpay sp. z o.o. <tech@dotpay.pl>.
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
 * @copyright Dotpay sp. z o.o.
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */

namespace Dotpay\Html\Container;

/**
 * Represent HTML div block.
 */
class Form extends Container
{
    /**
     * Initialize the block.
     *
     * @param array $children Children contained in the container
     */
    public function __construct($children = [])
    {
        parent::__construct('form', $children);
    }

    /**
     * Return a target URL.
     *
     * @return string
     */
    public function getAction()
    {
        return $this->getAttribute('action');
    }

    /**
     * Return an HTTP method.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->getAttribute('method');
    }

    /**
     * Set an action - target URL.
     *
     * @param string $action Target URL
     *
     * @return mixed
     */
    public function setAction($action)
    {
        return $this->setAttribute('action', (string) $action);
    }
    /**
     * Set an HTTP method.
     *
     * @param string $method Name of used HTTP method
     *
     * @return mixed
     */
    public function setMethod($method)
    {
        return $this->setAttribute('method', (string) $method);
    }
}
