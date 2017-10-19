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

namespace Dotpay\Provider;

/**
 * Interface of customer data providers from shop.
 */
interface CustomerProviderInterface
{
    /**
     * Return an id of the customer in a shop.
     *
     * @return int|null
     */
    public function getId();

    /**
     * Return an email address of the payer.
     *
     * @return string
     */
    public function getEmail();

    /**
     * Return a first name of the payer.
     *
     * @return string
     */
    public function getFirstName();

    /**
     * Return a last name of the payer.
     *
     * @return string
     */
    public function getLastName();

    /**
     * Return a street name of the customer.
     *
     * @return string
     */
    public function getStreet();

    /**
     * Return a building number of the customer.
     *
     * @return string
     */
    public function getBuildingNumber();

    /**
     * Return a post code of the customer.
     *
     * @return string
     */
    public function getPostCode();

    /**
     * Return a city of the customer.
     *
     * @return string
     */
    public function getCity();

    /**
     * Return a country of the customer.
     *
     * @return string
     */
    public function getCountry();

    /**
     * Return a phone number of the customer.
     *
     * @return string
     */
    public function getPhone();

    /**
     * Return a language used by the customer.
     *
     * @return string
     */
    public function getLanguage();
}
