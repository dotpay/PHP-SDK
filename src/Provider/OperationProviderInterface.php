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
 * Interface of operation data providers from shop.
 */
interface OperationProviderInterface
{
    /**
     * Return an account id of a seller.
     *
     * @return int|null
     */
    public function getAccountId();

    /**
     * Return a number of the operation.
     *
     * @return string
     */
    public function getNumber();

    /**
     * Return an identifier of a type of the operation.
     *
     * @return string
     */
    public function getType();

    /**
     * Return a status identifier of the operation.
     *
     * @return string
     */
    public function getStatus();

    /**
     * Return a transaction amount.
     *
     * @return float|null
     */
    public function getAmount();

    /**
     * Return a code of a transaction currency.
     *
     * @return string
     */
    public function getCurrency();

    /**
     * Return a withdrawal amount.
     *
     * @return float|null
     */
    public function getWithdrawalAmount();

    /**
     * Return an amount of a Dotpay commission.
     * It's presented as a negative amount.
     *
     * @return float|null
     */
    public function getCommissionAmount();

    /**
     * Return a flag if operation is marked as completed in Seller panel.
     *
     * @return bool
     */
    public function getCompleted();

    /**
     * Return an original amount which was sent from a shop.
     *
     * @return float|null
     */
    public function getOriginalAmount();

    /**
     * Return a code of an original currency which was sent from a shop.
     *
     * @return string
     */
    public function getOriginalCurrency();

    /**
     * Return a DateTime object with date and a time of the last change status of the operation.
     *
     * @return DateTime|null
     */
    public function getDateTime();

    /**
     * Return a number of an operation which is related to the operation.
     *
     * @return string
     */
    public function getRelatedNumber();

    /**
     * Return a value which was given during making a payment.
     *
     * @return mixed
     */
    public function getControl();

    /**
     * Return a description of the operation.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Return an email of payer.
     *
     * @return string
     */
    public function getEmail();
}
