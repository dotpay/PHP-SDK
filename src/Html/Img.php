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
 * Represent a HTML img element.
 */
class Img extends Single
{
    /**
     * Initialize the img element.
     *
     * @param string $src An url of the image
     */
    public function __construct($src)
    {
        parent::__construct('img');
        $this->setSrc($src);
    }

    /**
     * Return the url address of the image.
     *
     * @return string
     */
    public function getSrc()
    {
        return $this->getAttribute('src');
    }

    /**
     * Return the alt text of the image.
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->getAttribute('alt');
    }

    /**
     * Set the url address of the image.
     *
     * @param string $src The url address of the image
     *
     * @return Img
     */
    public function setSrc($src)
    {
        return $this->setAttribute('src', $src);
    }

    /**
     * Set the alt text of the image.
     *
     * @param string $alt The alt text of the image
     *
     * @return Img
     */
    public function setAlt($alt)
    {
        return $this->setAttribute('alt', $alt);
    }
}
