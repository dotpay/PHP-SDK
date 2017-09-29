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

namespace Dotpay\Processor;

use Dotpay\Exception\Payment\BlockedAccountException;
use Dotpay\Exception\Payment\DisabledChannelException;
use Dotpay\Exception\Payment\UnknownCurrencyException;
use Dotpay\Exception\Payment\HashNotEqualException;
use Dotpay\Exception\Payment\HighAmountException;
use Dotpay\Exception\Payment\InactiveAccountException;
use Dotpay\Exception\Payment\LowAmountException;
use Dotpay\Exception\Payment\ExpiredException;
use Dotpay\Exception\Payment\UnknowChannelException;
use Dotpay\Exception\Payment\BadDataFormatException;
use Dotpay\Exception\Payment\ParameterNotPresentException;
use Dotpay\Exception\Payment\Multimerchant\AccountConfigurationException;
use Dotpay\Exception\Payment\Multimerchant\InsufficientAmountException;
use Dotpay\Exception\Payment\Multimerchant\WrongCurrencyException;
use Dotpay\Exception\Payment\UnrecognizedException;

/**
 * Processor of service of back after making a payment.
 */
class Back
{
    /**
     * @var string/null Error code from Dotpay
     */
    private $errorCode = null;

    /**
     * Initialize the processor.
     *
     * @param string/null $errorCode Error code from Dotpay
     */
    public function __construct($errorCode)
    {
        if (!empty($errorCode)) {
            $this->errorCode = strtoupper($errorCode);
        }
    }

    /**
     * Execute the processor for making all activities.
     *
     * @return bool
     *
     * @throws ExpiredException              Thrown when payment has been expired
     * @throws UnknowChannelException        Thrown when the given channel is unknown
     * @throws DisabledChannelException      Thrown when selected channel payment is desabled
     * @throws UnknownCurrencyException      Thrown when currency code is unknown
     * @throws BlockedAccountException       Thrown when seller account is disabled
     * @throws InactiveAccountException      Thrown when seller account is inactive
     * @throws LowAmountException            Thrown when amount is too low
     * @throws HighAmountException           Thrown when amount is too high
     * @throws BadDataFormatException        Thrown when format of request data is bad
     * @throws ParameterNotPresentException  Thrown when one of neccessary parameters is not present
     * @throws AccountConfigurationException Thrown when one of multimerchant account is not configured to use this functionality
     * @throws InsufficientAmountException   Thrown when amount in multimerchant payments is wrong
     * @throws WrongCurrencyException        Thrown when currency in multimerchant payments is wrong
     * @throws HashNotEqualException         Thrown when request has been modified during transmission
     * @throws UnrecognizedException         Thrown when unrecognized error occured
     */
    public function execute()
    {
        if ($this->errorCode === null) {
            return true;
        }
        switch ($this->errorCode) {
            case 'PAYMENT_EXPIRED':
                throw new ExpiredException();
            case 'UNKNOWN_CHANNEL':
                throw new UnknowChannelException();
            case 'DISABLED_CHANNEL':
                throw new DisabledChannelException();
            case 'UNKNOWN_CURRENCY':
                throw new UnknownCurrencyException();
            case 'BLOCKED_ACCOUNT':
                throw new BlockedAccountException();
            case 'INACTIVE_SELLER':
                throw new InactiveAccountException();
            case 'AMOUNT_TOO_LOW':
                throw new LowAmountException();
            case 'AMOUNT_TOO_HIGH':
                throw new HighAmountException();
            case 'BAD_DATA_FORMAT':
                throw new BadDataFormatException();
            case 'REQUIRED_PARAMETERS_NOT_PRESENT':
                throw new ParameterNotPresentException();
            case 'MULTIMERCHANT_INVALID_ACCOUNT_CONFIGURATION':
                throw new AccountConfigurationException();
            case 'MULTIMERCHANT_INSUFFICIENT_AMOUNT':
                throw new InsufficientAmountException();
            case 'MULTIMERCHANT_WRONG_CURRENCY':
                throw new WrongCurrencyException();
            case 'HASH_NOT_EQUAL_CHK':
                throw new HashNotEqualException();
            default:
                throw new UnrecognizedException();
        }
    }
}
