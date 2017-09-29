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

namespace Dotpay\Provider;

/**
 * Interface of instruction data providers from shop.
 */
interface InstructionProviderInterface
{
    /**
     * Return an id of the instruction in a shop.
     *
     * @return int|null
     */
    public function getId();

    /**
     * Return id of order which is connected with the instruction.
     *
     * @return int|null
     */
    public function getOrderId();

    /**
     * Return a number of payment.
     *
     * @return string
     */
    public function getNumber();

    /**
     * Return a bank account number of Dotpay if the instruction applies to transfers payment.
     *
     * @return string|null
     */
    public function getBankAccount();

    /**
     * Return an id of channel which is used to make a payment.
     *
     * @return int|null
     */
    public function getChannel();

    /**
     * Return a hash of payment which is used on Dotpay server.
     *
     * @return string
     */
    public function getHash();

    /**
     * Return an amount of payment.
     *
     * @return float
     */
    public function getAmount();

    /**
     * Return a currency code of payment.
     *
     * @return string
     */
    public function getCurrency();
}
