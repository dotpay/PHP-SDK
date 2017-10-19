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

namespace Dotpay\Loader\Xml;

/**
 * Param node in XML file with Dependency Injection rules.
 * It represents parameter, which is given during creating an object.
 */
class Param
{
    /**
     * @var string A name of a class to which belongs an object which is a value of the parameter
     */
    private $className;

    /**
     * @var string A name of the parameter
     */
    private $name;

    /**
     * @var string An initial value of the parameter
     */
    private $value;

    /**
     * @var mixed A value which is stored in this parameter.
     *            It can be set after initialization of the object
     *            and it can store for example an instance of other class
     */
    private $storedValue;

    /**
     * Initialize the param object.
     *
     * @param string $className
     * @param string $name
     * @param mixed  $value
     */
    public function __construct($className = '', $name = '', $value = '')
    {
        $this->className = (string) $className;
        $this->name = (string) $name;
        $this->value = (string) $value;
    }

    /**
     * Return a class name of an object which is a value of the param.
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Return a name of the param.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return a value which is set during initialization of the param object.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Return a store value which is stored in the param object.
     * If a special value is set, then it's returned.
     * In other case can be returned a value which is set during initialization.
     * If none value is set, then will be returned a null value.
     *
     * @return mixed
     */
    public function getStoredValue()
    {
        if (!empty($this->storedValue)) {
            return $this->storedValue;
        }
        $value = $this->getValue();
        if (!empty($value)) {
            return $value;
        } else {
            return null;
        }
    }

    /**
     * Set a value which can be stored in the parameter, after initialization the object.
     *
     * @param mixed $value A value which to store in the param object
     *
     * @return Param
     */
    public function setStoredValue($value)
    {
        $this->storedValue = $value;

        return $this;
    }

    /**
     * Return a string which contains XML representation of the param object.
     *
     * @return string
     */
    public function getXml()
    {
        $element = '<param';
        $className = $this->getClassName();
        if (!empty($className)) {
            $element .= ' class=\''.$className.'\'';
        }
        $name = $this->getName();
        if (!empty($name)) {
            $element .= ' name=\''.$name.'\'';
        }
        $value = $this->getValue();
        if (!empty($value)) {
            $element .= ' value=\''.$value.'\'';
        }
        $element .= ' />';

        return $element;
    }
}
