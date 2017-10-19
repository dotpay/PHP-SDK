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
use Dotpay\Exception\BadParameter\AmountException;

/**
 * Informations about a payout transfer.
 */
class Transfer
{
    /**
     * @var float Amount of money
     */
    private $amount;

    /**
     * @var mixed Control identifier for the transfer
     */
    private $control;

    /**
     * @var BankAccount Bank account of the recipient
     */
    private $recipient;

    /**
     * @var string Description of the transfer
     */
    private $description;

    /**
     * Initialize the model.
     *
     * @param float       $amount
     * @param mixed       $control
     * @param BankAccount $recipient
     */
    public function __construct($amount, $control, BankAccount $recipient)
    {
        $this->setAmount($amount);
        $this->setControl($control);
        $this->setRecipient($recipient);
    }

    /**
     * Return an amount of money.
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Return a control identifier for the transfer.
     *
     * @return mixed
     */
    public function getControl()
    {
        return $this->control;
    }

    /**
     * Return a bank account of the recipient.
     *
     * @return BankAccount
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * Return a description of the transfer.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set an amount of money.
     *
     * @param float $amount Amount of money
     *
     * @return Transfer
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
     * Set a control identifier for the transfer.
     *
     * @param mixed $control Control identifier for the transfer
     *
     * @return Transfer
     */
    public function setControl($control)
    {
        $this->control = $control;

        return $this;
    }

    /**
     * Set a bank account of the recipient.
     *
     * @param BankAccount $recipient Bank account of the recipient
     *
     * @return Transfer
     */
    public function setRecipient(BankAccount $recipient)
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * Set a description of the transfer.
     *
     * @param string $description Description of the transfer
     *
     * @return Transfer
     */
    public function setDescription($description)
    {
        $this->description = (string) $description;

        return $this;
    }
}
