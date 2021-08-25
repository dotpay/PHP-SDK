<?php
/**
 * Copyright (c) 2021 PayPro S.A. <tech@dotpay.pl>.
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
 * @copyright PayPro S.A.
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */

namespace Dotpay\Model;

use Dotpay\Validator\Name;
use Dotpay\Validator\Email;
use Dotpay\Exception\BadParameter\FirstnameException;
use Dotpay\Exception\BadParameter\LastnameException;
use Dotpay\Exception\BadParameter\EmailException;

/**
 * Informations about a payer.
 */
class Payer
{
    /**
     * @var string Email address of the payer
     */
    private $email;

    /**
     * @var string First name of the payer
     */
    private $firstName = '';

    /**
     * @var string Last name of the payer
     */
    private $lastName = '';

    /**
     * Initialize the model.
     *
     * @param string $email     Email address of the payer
     * @param string $firstName First name of the payer
     * @param string $lastName  Last name of the payer
     */
    public function __construct($email, $firstName = '', $lastName = '')
    {
        $this->setEmail($email);
        if (!empty($firstName)) {
            $this->setFirstName($firstName);
        }
        if (!empty($lastName)) {
            $this->setLastName($lastName);
        }
    }

    /**
     * Return an email address of the payer.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Return a first name of the payer.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Return a last name of the payer.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set the email address of the payer.
     *
     * @param type $email Email address of the payer
     *
     * @return Payer
     *
     * @throws EmailException Thrown when the given email address is incorrect
     */
    public function setEmail($email)
    {
        if (!Email::validate($email)) {
            throw new EmailException($email);
        }
        $this->email = (string) $email;

        return $this;
    }

    /**
     * Set the first name of the payer.
     *
     * @param string $firstName First name of the payer
     *
     * @return Payer
     *
     * @throws FirstnameException Thrown when the given first name is incorrect
     */
    public function setFirstName($firstName)
    {
        if (!Name::validate($firstName)) {
            throw new FirstnameException($firstName);
        }
        $this->firstName = (string) $firstName;

        return $this;
    }

    /**
     * Set the last name of the payer.
     *
     * @param type $lastName Last name of the payer
     *
     * @return Payer
     *
     * @throws LastnameException Thrown when the given last name is incorrect
     */
    public function setLastName($lastName)
    {
        if (!Name::validate($lastName)) {
            throw new LastnameException($lastName);
        }
        $this->lastName = (string) $lastName;

        return $this;
    }
}
