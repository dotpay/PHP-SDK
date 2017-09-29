<?php
/**
 * Copyright (c) 2017 Dotpay S.A. <techdotpay.pl>.
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

namespace Dotpay\Html\Form;

use Dotpay\Html\Container\Container;

/**
 * Represent a HTML select element.
 */
class Select extends Container
{
    /**
     * @var Option An option element which is set as a selected option
     */
    private $selected;

    /**
     * Initialize the select element.
     *
     * @param string $name     A name of the select element
     * @param array  $options  An array of options element which belong to the select element
     * @param Option $selected An option element which is set as a selected option
     */
    public function __construct($name = '', array $options = [], $selected = null)
    {
        parent::__construct('select', $options);
        if (!empty($name)) {
            $this->setName($name);
        }
        if (!empty($selected)) {
            $this->setSelected($selected);
        }
    }

    /**
     * Return an option element which is set as a selected option.
     *
     * @return Option
     */
    public function getSelected()
    {
        return $this->selected;
    }

    /**
     * Return an array of options element which belong to the select element.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->getChildren();
    }

    /**
     * Set an option element which contains the given value as the seleced option in this select element.
     *
     * @param mixed $value An value which can be also a text from an option element
     *
     * @return Select
     */
    public function setSelected($value)
    {
        foreach ($this->getChildren() as $option) {
            if ($this->checkValue($option, $value)) {
                $this->selected = $option->setSelected();
            } else {
                $option->setSelected(false);
            }
        }

        return $this;
    }

    /**
     * Add a new option element to a list of options which belong to the select element.
     *
     * @param Option $option An option to add
     *
     * @return Select
     */
    public function addOption(Option $option)
    {
        return $this->addChild($option);
    }

    /**
     * Remove an option element which contains the given value from the list of all options.
     *
     * @param mixed $value An value which can be also a text from an option element
     *
     * @return Select
     */
    public function removeOption($value)
    {
        foreach ($this->getChildren() as $option) {
            if ($this->checkValue($option, $value)) {
                $this->removeChild($option);
                break;
            }
        }

        return $this;
    }

    /**
     * Check if the option element contains the given value or if the given value is a text of the option.
     *
     * @param Option $option An option element
     * @param mixed  $value  A given value
     *
     * @return bool
     */
    private function checkValue(Option $option, $value)
    {
        return $option->getValue() === $value ||
               ($option->getValue() === null && $option->getText() === $value);
    }
}
