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

namespace Dotpay\Provider;

/**
 * Interface of notification data providers from shop.
 */
interface CreditCardProviderInterface
{
    /**
     * Return a CardBrand object with details of a credit card brand.
     *
     * @return \Dotpay\Model\CardBrand
     */
    public function getBrand();

    /**
     * Return id of credit card issuer.
     *
     * @return string
     */
    public function getIssuerId();

    /**
     * Return a masked number of the credt card.
     *
     * @return string
     */
    public function getMask();

    /**
     * Return a unique identifier of the credit card.
     *
     * @return int
     */
    public function getUniqueId();

    /**
     * Return an identificator of credit card which is assigned by Dotpay system.
     *
     * @return string
     */
    public function getCardId();

    /**
     * Return credit card's expiration year.
     *
     * @return string
     */
    public function getExpirationYear();

    /**
     * Return credit card's expiration month.
     *
     * @return string
     */
    public function getExpirationMonth();
}
