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

namespace Dotpay\Model;

use Dotpay\Validator\Amount;
use Dotpay\Validator\OpNumber;
use Dotpay\Exception\BadParameter\AmountException;
use Dotpay\Exception\BadParameter\OperationNumberException;

/**
 * Model of refund data.
 */
class Refund
{
    /**
     * @var string Number of payment for which is the refund
     */
    private $payment;

    /**
     * @var float Amount of refund
     */
    private $amount;

    /**
     * @var string Value which is used by comfirmation of the operation
     */
    private $control = '';

    /**
     * @var string Descriptiion of the refund
     */
    private $description = '';

    /**
     * Initialize the object model.
     *
     * @param string $payment     Number of payment which is refunded
     * @param float  $amount      Amount of the refund
     * @param string $control     Value which is used by comfirmation of the operation
     * @param string $description Description of refund
     */
    public function __construct($payment, $amount, $control = '', $description = '')
    {
        $this->setPayment($payment);
        $this->setAmount($amount);
        $this->setControl($control);
        $this->setDescription($description);
    }

    /**
     * Return a number of payment for which is the refund.
     *
     * @return string
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Return an amount of refund.
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Return a value which is used by comfirmation of the operation.
     *
     * @return mixed
     */
    public function getControl()
    {
        return $this->control;
    }

    /**
     * Return a descriptiion of the refund.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set a number of payment for which is the refund.
     *
     * @param string $payment Number of payment for which is the refund
     *
     * @return Refund
     *
     * @throws OperationNumberException Thrown when the given operation number is incorrect
     */
    public function setPayment($payment)
    {
        if (!OpNumber::validate($payment)) {
            throw new OperationNumberException($payment);
        }
        $this->payment = (string) $payment;

        return $this;
    }

    /**
     * Set an amount of refund.
     *
     * @param float $amount Amount of refund
     *
     * @return Refund
     *
     * @throws AmountException Thrown when the given amount is incorrect
     */
    public function setAmount($amount)
    {
        if (!Amount::validate($amount)) {
            throw new AmountException($amount);
        }
        $this->amount = floatval(str_replace(' ','',$amount));

        return $this;
    }

    /**
     * Set a value which is used by comfirmation of the operation.
     *
     * @param mixed $control Value which is used by comfirmation of the operation
     *
     * @return Refund
     */
    public function setControl($control)
    {
        $this->control = (string) $control;

        return $this;
    }

    /**
     * Set a description of the refund.
     *
     * @param string $description Descriptiion of the refund
     *
     * @return Refund
     */
    public function setDescription($description)
    {
        $this->description = (string) $description;

        return $this;
    }
}
