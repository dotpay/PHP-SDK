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

namespace Dotpay\Html\Form;

use Dotpay\Html\Node;

/**
 * Represent a HTML label element.
 */
class Label extends Node
{
    /**
     * @var Node An element which is inside the label
     */
    private $element;

    /**
     * @var string A text which is displayed on the left side of the inside element
     */
    private $llabel;

    /**
     * @var string A text which is displayed on the right side of the inside element
     */
    private $rlabel;

    /**
     * Initialize the label element.
     *
     * @param Node   $element An element which is inside the label
     * @param string $llabel  A text which is displayed on the left side of the inside element
     * @param string $rlabel  A text which is displayed on the right side of the inside element
     */
    public function __construct(Node $element, $llabel = '', $rlabel = '')
    {
        $this->element = $element;
        $this->setLLabel($llabel);
        $this->setRLabel($rlabel);
    }

    /**
     * Return an element which is inside the label.
     *
     * @return Node
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * Return a text which is displayed on the left side of the inside element.
     *
     * @return string
     */
    public function getLLabel()
    {
        return $this->llabel;
    }

    /**
     * Return a text which is displayed on the right side of the inside element.
     *
     * @return string
     */
    public function getRLabel()
    {
        return $this->rlabel;
    }

    /**
     * Set a text which is displayed on the left side of the inside element.
     *
     * @param string $label A text to displaying on the left side
     *
     * @return Label
     */
    public function setLLabel($label)
    {
        $this->llabel = $label;

        return $this;
    }

    /**
     * Set a text which is displayed on the right side of the inside element.
     *
     * @param string $label A text to displaying on the right side
     *
     * @return Label
     */
    public function setRLabel($label)
    {
        $this->rlabel = $label;

        return $this;
    }

    /**
     * Return a HTML string of the label.
     *
     * @return string
     */
    public function __toString()
    {
        return '<label'.
                $this->getAttributeList().
                '>'.
                $this->getLLabel().
                (string) $this->getElement().
                $this->getRLabel().
                '</label>';
    }
}
