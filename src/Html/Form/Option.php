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

use Dotpay\Html\Container\Container;
use Dotpay\Html\PlainText;

/**
 * Represent an option of an HTML select element.
 */
class Option extends Container
{
    /**
     * Initialize the option element.
     *
     * @param string $text  A text to displaying in the option element
     * @param mixed  $value A value of the option element
     */
    public function __construct($text, $value = null)
    {
        parent::__construct('option');
        $this->setText($text);
        if (!empty($value)) {
            $this->setValue($value);
        }
    }

    /**
     * Return a value of the option element.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->getAttribute('value');
    }

    /**
     * Return a text to displaying in the option element.
     *
     * @return PlainText
     */
    public function getText()
    {
        $children = $this->getChildren();

        return $children[0];
    }

    /**
     * Set a value of the option element.
     *
     * @param mixed $value A value which is set
     *
     * @return Option
     */
    public function setValue($value)
    {
        return $this->setAttribute('value', $value);
    }

    /**
     * Set a flag if the option element is selected.
     *
     * @param bool $mode A flad if the option element is selected
     *
     * @return Option
     */
    public function setSelected($mode = true)
    {
        if ($mode) {
            return $this->setAttribute('selected', 'selected');
        } else {
            return $this->removeAttribute('selected');
        }
    }

    /**
     * Check if the option element is selected.
     *
     * @return bool
     */
    public function isSelected()
    {
        return (bool) $this->getAttribute('selected');
    }

    /**
     * Set a text to displaying in the option element.
     *
     * @param string $text A text to displaying in the option element
     *
     * @return Option
     */
    public function setText($text)
    {
        if (!empty($text)) {
            if (!($text instanceof PlainText)) {
                $text = new PlainText($text);
            }
            $this->setChildren([$text]);
        }

        return $this;
    }
}
