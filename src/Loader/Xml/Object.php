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

namespace Dotpay\Loader\Xml;

use Dotpay\Exception\Loader\EmptyObjectNameException;

/**
 * Object node in XML file with Dependency Injection rules.
 * It represents created object.
 */
class Object
{
    /**
     * @var string Class name of the object
     */
    private $className;

    /**
     * @var array Array of Param object which are used during creation the object
     */
    private $parameters = [];

    /**
     * @var array Array of named prameters which contain Param objects
     */
    private $namedParameters = [];

    /**
     * @var string|null A short name of the object. It's an alias on the main class name
     */
    private $alias;

    /**
     * @var bool A flag if the instance should be always new
     */
    private $alwaysNew = false;

    /**
     * @var array Array of stored instances of the object for different sets of params used for an initialization
     */
    private $storedInstance = [];

    /**
     * Initialize the object.
     *
     * @param string      $className  Class name of the object
     * @param array       $parameters Array of Param object which are used during creation the object
     * @param string|null $alias      A short name of the object
     * @param bool A flag if the instance should be always new
     *
     * @throws EmptyObjectNameException Thrown when class name is empty
     */
    public function __construct($className, array $parameters = [], $alias = null, $alwaysNew = false)
    {
        if (empty($className)) {
            throw new EmptyObjectNameException();
        }
        $this->className = (string) $className;
        foreach ($parameters as $param) {
            if ($param instanceof Param) {
                $this->parameters[] = $param;
                $name = $param->getName();
                if (!empty($name)) {
                    $this->namedParameters[$name] = $param;
                }
            }
        }
        $this->alias = (string) $alias;
        $this->alwaysNew = (bool) $alwaysNew;
    }

    /**
     * Return a class name of the object.
     *
     * @return string
     */
    public function getClass()
    {
        return $this->className;
    }

    /**
     * Return an array of all Param objects for the object.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->parameters;
    }

    /**
     * Return a value of a parameter which has the given name, if the parameter is in a set of named parameters.
     *
     * @param string $name Name of the parameter
     *
     * @return mixed
     */
    public function getParamVal($name)
    {
        foreach ($this->namedParameters as $key => $value) {
            if ($name === $key) {
                return $value->getStoredValue();
            }
        }

        return null;
    }

    /**
     * Set a value to the parameter which has the given name, if the parameter is in a set of named parameters.
     *
     * @param string $name  A name of the parameter
     * @param mixed  $value A value to set
     *
     * @return object
     */
    public function setParamVal($name, $value)
    {
        foreach ($this->namedParameters as $key => $oldValue) {
            if ($name === $key) {
                $this->namedParameters[$name]->setStoredValue($value);
                break;
            }
        }

        return $this;
    }

    /**
     * Return an alias of the object.
     *
     * @return string|null
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Return a flag if the instance should be always new.
     *
     * @return bool
     */
    public function getAlwaysNew()
    {
        return $this->alwaysNew;
    }

    /**
     * Return an object which was created with the given set of params and which is set as an one of instances inside the Object.
     *
     * @param array $params An array of params
     *
     * @return object|null
     */
    public function getStoredInstance(array $params)
    {
        $paramId = sha1($this->getParamsId($params));

        return isset($this->storedInstance[$paramId]) ? $this->storedInstance[$paramId] : null;
    }

    /**
     * Set the instance of an object which was created with using the given set of params.
     *
     * @param array  $params   Params which were used to create the instance
     * @param object $instance An instance of a object which is the instance of the class represents by the Object
     *
     * @return object
     */
    public function setStoredInstance($params, $instance)
    {
        $paramId = sha1($this->getParamsId($params));
        $this->storedInstance[$paramId] = $instance;

        return $this;
    }

    /**
     * Return a string which contains XML representation of the Object.
     *
     * @return string
     */
    public function getXml()
    {
        $element = '<object';
        if (!empty($this->getClass())) {
            $element .= ' class=\''.$this->getClass().'\'';
        }
        if (!empty($this->getAlias())) {
            $element .= ' alias=\''.$this->getAlias().'\'';
        }
        $element .= '>';
        foreach ($this->getParams() as $param) {
            $element .= $param->getXml();
        }
        $element .= '</object>';

        return $element;
    }

    /**
     * Return an identificator of the given data. It's a substitute of full serialization.
     *
     * @param mixed $input Input data
     *
     * @return string
     */
    private function getParamsId($input)
    {
        switch (gettype($input)) {
        case 'object':
            return get_class($input);
        case 'array':
            $serialString = '';
            foreach ($input as $key => $value) {
                $serialString .= $key.$this->getParamsId($value);
            }

            return $serialString;
        case 'resource':
            return get_resource_type($input);
        case 'unknown type':
            return 'unknown';
        case 'NULL':
            return 'null';
        default:
            return $input;
    }
    }
}
