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

namespace Dotpay\Model;

use Dotpay\Exception\BadParameter\CurrencyException;

/**
 * Informations about a payout.
 */
class Payout
{
    /**
     * @var string|null Currency code
     */
    private $currency = null;

    /**
     * @var array List of transfers to realize
     */
    private $transfers = [];

    /**
     * Initialize the model.
     *
     * @param string $currency Currency code
     */
    public function __construct($currency)
    {
        $this->setCurrency($currency);
    }

    /**
     * Return a currency code.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Return a list of transfers to realize.
     *
     * @return array
     */
    public function getTransfers()
    {
        return $this->transfers;
    }

    /**
     * Set a currency code.
     *
     * @param string $currency Currency code
     *
     * @return Payout
     *
     * @throws CurrencyException Thrown when the given currency code is incorrect
     */
    public function setCurrency($currency)
    {
        $currency = strtoupper($currency);
        if (!in_array($currency, Configuration::$CURRENCIES)) {
            throw new CurrencyException($currency);
        }
        $this->currency = (string) $currency;

        return $this;
    }

    /**
     * Add a new Transfer object to the list.
     *
     * @param Transfer $transfer A Transfer object
     *
     * @return Payout
     */
    public function addTransfer(Transfer $transfer)
    {
        $this->transfers[] = $transfer;

        return $this;
    }
}
