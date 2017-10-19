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

namespace Dotpay\Model;

use Dotpay\Validator\Url;
use Dotpay\Exception\BadParameter\UrlException;

/**
 * Informations about a card brand.
 */
class CardBrand
{
    /**
     * @var int|null Id of the brand in the database
     */
    private $id = null;

    /**
     * @var string Name of the brand of a credit card
     */
    private $name;

    /**
     * @var string|null Code name of the brand of a credit card
     */
    private $codeName = null;

    /**
     * @var string Url to the logo of the credit card brand
     */
    private $image;

    /**
     * Initialize the model.
     *
     * @param string      $name     Name of the brand of a credit card
     * @param string      $image    Url to the logo of the credit card brand
     * @param string|null $codeName Code name of the brand of a credit card
     */
    public function __construct($name, $image, $codeName = null)
    {
        $this->setName($name);
        $this->setImage($image);
        $this->setCodeName($codeName);
    }

    /**
     * Return a name of the brand of a credit card.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return a code name of the brand of a credit card.
     *
     * @return string|null
     */
    public function getCodeName()
    {
        return $this->codeName;
    }

    /**
     * Return a url to the logo of the credit card brand.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set a code name of the brand of a credit card.
     *
     * @param string|null $name Name of the brand of a credit card
     *
     * @return CardBrand
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set a code name of the brand of a credit card.
     *
     * @param string|null $codeName Code name of the brand of a credit card
     *
     * @return CardBrand
     */
    public function setCodeName($codeName)
    {
        $this->codeName = $codeName;

        return $this;
    }

    /**
     * Set a url to the logo of the credit card brand.
     *
     * @param string|null $image Url to the logo of the credit card brand
     *
     * @return CardBrand
     *
     * @throws UrlException Thrown when the given url address is incorrect
     */
    public function setImage($image)
    {
        if (!Url::validate($image)) {
            throw new UrlException($image);
        }
        $this->image = $image;

        return $this;
    }
}
