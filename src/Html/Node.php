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

namespace Dotpay\Html;

/**
 * Represent an abstract HTML node.
 */
abstract class Node
{
    /**
     * @var array List of all attributes of the node
     */
    private $attributes = [];

    /**
     * Return a value of the attribute whoose name is given.
     *
     * @param string $name The name of the attribute
     *
     * @return mixed
     */
    public function getAttribute($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    /**
     * Set a value of the atribute whoose name is given.
     *
     * @param string $name  The name of the attribute
     * @param mixed  $value The value of the attribute
     *
     * @return Node
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * Remove an attribute whoose name is given.
     *
     * @param string $name The name of the attribute
     *
     * @return Node
     */
    public function removeAttribute($name)
    {
        if (isset($this->attributes[$name])) {
            unset($this->attributes[$name]);
        }

        return $this;
    }

    /**
     * Return an array with all attributes of the node element.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Return a string with a list of all attributes ot the node element.
     *
     * @return string
     */
    protected function getAttributeList()
    {
        $html = '';
        foreach ($this->getAttributes() as $name => $value) {
            $html .= ' '.$name.'=\''.$value.'\'';
        }

        return $html;
    }

    /**
     * Return a HTML string of the node element.
     *
     * @return string
     */
    public function __toString()
    {
        return '';
    }
}
