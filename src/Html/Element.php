<?php
/**
 * Copyright (c) 2017 Dotpay S.A. <tech@dotpay.pl>.
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
 * @copyright Dotpay S.A
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */

namespace Dotpay\Html;

/**
 * Represent an abstract HTML element.
 */
abstract class Element extends Node
{
    /**
     * @var string A type of the element
     */
    private $type;

    /**
     * Initialize the element.
     *
     * @param string $type A type of the element
     * @param string $name A name of the element
     */
    public function __construct($type = '', $name = null)
    {
        $this->setType($type);
        if ($name !== null) {
            $this->setName($name);
        }
    }

    /**
     * Return a type of the element.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return a name of the element.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getAttribute('name');
    }

    /**
     * Return a class name of the element.
     *
     * @return string
     */
    public function getClass()
    {
        return $this->getAttribute('class');
    }

    /**
     * Return a data information.
     *
     * @param string $name A name of data information
     *
     * @return string
     */
    public function getData($name)
    {
        return $this->getAttribute('data-'.$name);
    }

    /**
     * Set a type of the element.
     *
     * @param string $type A type of the element
     *
     * @return Element
     */
    private function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set a name of the element.
     *
     * @param string $name A name of the element
     *
     * @return Element
     */
    public function setName($name)
    {
        return $this->setAttribute('name', $name);
    }

    /**
     * Set a class name of the element.
     *
     * @param string $className A class name
     *
     * @return Element
     */
    public function setClass($className)
    {
        return $this->setAttribute('class', $className);
    }

    /**
     * Set the data value as the given name.
     *
     * @param string $name  A name of a value
     * @param string $value A value to saving
     *
     * @return Element
     */
    public function setData($name, $value)
    {
        return $this->setAttribute('data-'.$name, $value);
    }

    /**
     * Return a HTML string of the element.
     *
     * @return string
     */
    public function __toString()
    {
        return parent::__toString();
    }
}
