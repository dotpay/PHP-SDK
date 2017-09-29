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

namespace Dotpay\Html;

/**
 * Represent a plain text which is inserted into HTML without any tags.
 */
class PlainText extends Node
{
    /**
     * @var string A content of the text element
     */
    private $text;

    /**
     * Initialize the plain text element.
     *
     * @param string $text A content of the text element
     */
    public function __construct($text = '')
    {
        $this->setText($text);
    }

    /**
     * Return a content of the text element.
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set a content of the text element.
     *
     * @param string $text A content of the text element
     *
     * @return PlainText
     */
    public function setText($text)
    {
        $this->text = (string) $text;

        return $this;
    }

    /**
     * Return a HTML string of the text element.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getText();
    }
}
