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

use Dotpay\Validator\BankNumber;
use Dotpay\Exception\BadParameter\BankNumberException;

/**
 * Informations about a bank acount of payer.
 */
class BankAccount
{
    /**
     * @var string|null Name of an owner of the bank account
     */
    private $name = null;

    /**
     * @var string|null Bank account number
     */
    private $number = null;

    /**
     * Initialize the model.
     *
     * @param string|null $name   Name of an owner of the bank account
     * @param string|null $number Bank account number
     */
    public function __construct($name = null, $number = null)
    {
        $this->setName($name);
        $this->setNumber($number);
    }

    /**
     * Return a name of an owner of the bank account.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return a bank account number.
     *
     * @return string|null
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set the given name of an owner of the bank account.
     *
     * @param string|null $name Name of an owner of the bank account
     *
     * @return BankAccount
     */
    public function setName($name)
    {
        if (empty($name)) {
            $name = null;
        }
        $this->name = $name;

        return $this;
    }

    /**
     * Set the given bank account number.
     *
     * @param string|null $number Bank account number
     *
     * @return BankAccount
     *
     * @throws BankNumberException Thrown when the given bank account number is incorrect
     */
    public function setNumber($number)
    {
        if (preg_match('/^\d{26}$/', trim($number)) === 1) {
            $number = 'PL'.$number;
        }
        if (!empty($number) && !BankNumber::validate($number)) {
            throw new BankNumberException($number);
        }
        if (empty($number)) {
            $number = null;
        }
        $this->number = $number;

        return $this;
    }
}
