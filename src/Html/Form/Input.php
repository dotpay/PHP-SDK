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

namespace Dotpay\Html\Form;

use Dotpay\Html\Single;

/**
 * Represent a HTML input element.
 */
class Input extends Single
{
    /**
     * Initialize the form element.
     *
     * @param string $type  Type of the input element
     * @param string $name  Name of the input element
     * @param mixed  $value Value of the input element
     */
    public function __construct($type, $name = '', $value = null)
    {
        $this->setInputType($type);
        parent::__construct('input', $name);
        if ($value !== null) {
            $this->setValue($value);
        }
    }

    /**
     * Return a type of the input element.
     *
     * @return string
     */
    public function getInputType()
    {
        return $this->getAttribute('type');
    }

    /**
     * Return a value of the input element.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->getAttribute('value');
    }

    /**
     * Set a type of the input element.
     *
     * @param string $type A type of the input element
     *
     * @return Input
     */
    public function setInputType($type)
    {
        return $this->setAttribute('type', $type);
    }

    /**
     * Set a type of the input element.
     *
     * @param mixed $value
     *
     * @return Input
     */
    public function setValue($value)
    {
        return $this->setAttribute('value', $value);
    }
}
