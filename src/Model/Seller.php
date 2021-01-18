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

use Dotpay\Validator\Id;
use Dotpay\Validator\Pin;
use Dotpay\Validator\Email;
use Dotpay\Validator\Username;
use Dotpay\Exception\BadParameter\IdException;
use Dotpay\Exception\BadParameter\PinException;
use Dotpay\Exception\BadParameter\UsernameException;
use Dotpay\Exception\BadParameter\EmailException;

/**
 * Informations about a seller.
 */
class Seller
{
    /**
     * @var int|null Seller id
     */
    private $id = null;

    /**
     * @var string Seller pin
     */
    private $pin = '';

    /**
     * @var bool A flag if seller uses test mode
     */
    private $testMode = false;

    /**
     * @var string Username of Dotpay seller dashboard
     */
    private $username = '';

    /**
     * @var string Password of Dotpay seller dashboard
     */
    private $password = '';

    /**
     * @var string Info about a shop name
     */
    private $info = '';

    /**
     * @var string Email of the seller
     */
    private $email = '';

    /**
     * Initialize the model.
     *
     * @param int    $id  Seller id
     * @param string $pin Seller pin
     * @param bool A flag if seller uses test mode
     */
    public function __construct($id, $pin, $testMode)
    {
        $this->setId($id);
        $this->setPin($pin);
        $this->setTestMode($testMode);
    }

    /**
     * Create normal seller object.
     *
     * @param Configuration $config Configuration object
     *
     * @return Seller
     */
    public static function createFromConfiguration(Configuration $config)
    {
        return new static($config->getId(),
                          $config->getPin(),
                          $config->getTestMode());
    }

    /**
     * Create seller object for FCC payments.
     *
     * @param Configuration $config Configuration object
     *
     * @return Seller
     */
    public static function createFccFromConfiguration(Configuration $config)
    {
        return new static($config->getFccId(),
                          $config->getFccPin(),
                          $config->getTestMode());
    }

    /**
     * Return a seller id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return a seller pin.
     *
     * @return string
     */
    public function getPin()
    {
        return $this->pin;
    }

    /**
     * Return a flag if seller uses test mode.
     *
     * @return bool
     */
    public function isTestMode()
    {
        return $this->testMode;
    }

    /**
     * Return a username of Dotpay seller dashboard.
     *
     * @return strung
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Return a password of Dotpay seller dashboard.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Return an info/email about a shop name.
     *
     * @return string
     */
    public function getInfo()
    {

        return $this->info;
      
    }

    /**
     * Return an email of the seller.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Check if an username and a password are given.
     *
     * @return bool
     */
    public function hasAccessToApi()
    {
        return !empty($this->username) && !empty($this->password);
    }

    /**
     * Set a seller id.
     *
     * @param int $id Seller id
     *
     * @return Seller
     *
     * @throws IdException Thrown when the given seller id is incorrect
     */
    public function setId($id)
    {
        if (!Id::validate($id)) {
            throw new IdException($id);
        }
        $this->id = (int) $id;

        return $this;
    }

    /**
     * Set a seller pin.
     *
     * @param string $pin Seller pin
     *
     * @return Seller
     *
     * @throws PinException Thrown when the given seller pin is incorrect
     */
    public function setPin($pin)
    {
        if (!Pin::validate($pin)) {
            throw new PinException($pin);
        }
        $this->pin = (string) $pin;

        return $this;
    }

    /**
     * Set a flag if seller uses test mode.
     *
     * @param bool $testMode Flag if seller uses test mode
     *
     * @return Seller
     */
    public function setTestMode($testMode)
    {
        $this->testMode = (bool) $testMode;

        return $this;
    }

    /**
     * Set a username of Dotpay seller dashboard.
     *
     * @param string $username Username of Dotpay seller dashboard
     *
     * @return Seller
     *
     * @throws UsernameException Thrown when the given seller username is incorrect
     */
    public function setUsername($username)
    {
        if (!Username::validate($username)) {
            throw new UsernameException($username);
        }
        $this->username = $username;

        return $this;
    }

    /**
     * Set a password of Dotpay seller dashboard.
     *
     * @param string $password Password of Dotpay seller dashboard
     *
     * @return Seller
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set an info about a shop name.
     *
     * @param string $info Info about a shop name
     *
     * @return Seller
     */
    public function setInfo($info)
    {

       $this->info = $info;


        return $this;
    }

    /**
     * Set an email of the seller.
     *
     * @param string $email Email of the seller
     *
     * @return Seller
     *
     * @throws EmailException Thrown when the given seller email address is incorrect
     */
    public function setEmail($email)
    {
        if (!Email::validate($email)) {
            throw new EmailException($email);
        }
        $this->email = $email;

        return $this;
    }
}
