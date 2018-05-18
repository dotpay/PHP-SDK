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

use DateTime;
use Dotpay\Validator\CardMask;
use Dotpay\Validator\Url;
use Dotpay\Exception\BadParameter\CardMaskException;
use Dotpay\Exception\BadParameter\UrlException;

/**
 * Informations about a credit card.
 */
class CreditCard
{
    /**
     * @var int|null Id of credit card in a shop
     */
    private $id = null;

    /**
     * @var string Card masked number
     */
    private $mask = '';

    /**
     * @var CardBrand|null Brand object for the credit card
     */
    private $brand = null;

    /**
     * @var string Identificator of a credit card user
     */
    private $userId = '';

    /**
     * @var string Identificator of credit card which is assigned by Dotpay system
     */
    private $cardId = '';

    /**
     * @var string Card issuer id
     */
    private $issuerId = '';

    /**
     * @var string Customer hash
     */
    private $customerHash = '';

    /**
     * @var string URL on Dotpay server where is located information about this card
     */
    private $href = '';

    /**
     * @var DateTime|null Date when the card has beed registered
     */
    private $registerDate = null;

    /**
     * @var int|null Id of the first order made using the card
     */
    private $orderId = null;

    /**
     * Initialize the model.
     *
     * @param int    $id     Id of credit card in a shop
     * @param string $userId Identificator of a credit card user
     */
    public function __construct($id, $userId)
    {
        $this->setId($id);
        $this->setUserId($userId);
    }

    /**
     * Return a credit card id in a shop.
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return a card masked number.
     *
     * @return string
     */
    public function getMask()
    {
        return $this->mask;
    }

    /**
     * Return a brand object for the credit card.
     *
     * @return CardBrand
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Return an identificator of a credit card user.
     *
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Return an identificator of credit card which is assigned by Dotpay system.
     *
     * @return type
     */
    public function getCardId()
    {
        return $this->cardId;
    }

    /**
     * Return a card issuer id.
     *
     * @return string
     */
    public function getIssuerId()
    {
        return $this->issuerId;
    }

    /**
     * Return a customer hash.
     *
     * @return string
     */
    public function getCustomerHash()
    {
        return $this->customerHash;
    }

    /**
     * Return an URL on Dotpay server where is located information about this card.
     *
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * Return a date when the card has beed registered.
     *
     * @return DateType|null
     */
    public function getRegisterDate()
    {
        return $this->registerDate;
    }

    /**
     * Return an id of the first order made using the card.
     *
     * @return int|null
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Check if the credit card contains information which are available after registration on Dotpay server.
     *
     * @return bool
     */
    public function isRegistered()
    {
        return !($this->getCardId() === '' ||
                $this->getBrand() === null ||
                $this->getMask() === '');
    }

    /**
     * Set an id of credit card in a shop.
     *
     * @param int $id Id of credit card in a shop
     *
     * @return CreditCard
     */
    public function setId($id)
    {
        $this->id = (int) $id;

        return $this;
    }

    /**
     * Set a card masked number.
     *
     * @param string $mask Card masked number
     *
     * @return CreditCard
     *
     * @throws CardMaskException Thrown when the given card masked number is incorrect
     */
    public function setMask($mask)
    {
        $mask = str_replace(' ', '-', strtoupper($mask));
        if (!CardMask::validate($mask)) {
            throw new CardMaskException($mask);
        }
        $this->mask = (string) $mask;

        return $this;
    }

    /**
     * Set a brand object for the credit card.
     *
     * @param CardBrand $brand Brand object for the credit card
     *
     * @return CreditCard
     */
    public function setBrand(CardBrand $brand)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Set an identificator of a credit card user.
     *
     * @param string $userId Identificator of a credit card user
     *
     * @return CreditCard
     */
    public function setUserId($userId)
    {
        $this->userId = (string) $userId;

        return $this;
    }

    /**
     * Set an identificator of credit card which is assigned by Dotpay system.
     *
     * @param string $cardId Identificator of credit card which is assigned by Dotpay system
     *
     * @return CreditCard
     */
    public function setCardId($cardId)
    {
        $this->cardId = (string) $cardId;

        return $this;
    }

    /**
     * Set a card issuer id.
     *
     * @param string $issuerId Card issuer id
     *
     * @return CreditCard
     */
    public function setIssuerId($issuerId)
    {
        $this->issuerId = (string) $issuerId;

        return $this;
    }

    /**
     * Set the given customer hash.
     *
     * @param string $customerHash Customer hash
     *
     * @return CreditCard
     */
    public function setCustomerHash($customerHash)
    {
        $this->customerHash = (string) $customerHash;

        return $this;
    }

    /**
     * Set an URL on Dotpay server where is located information about this card.
     *
     * @param string $href Url where are located information about this card
     *
     * @return CreditCard
     *
     * @throws UrlException Thrown when the given URl address is incorrect
     */
    public function setHref($href)
    {
        if (!Url::validate($href)) {
            throw new UrlException($href);
        }
        $this->href = (string) $href;

        return $this;
    }

    /**
     * Set a date when the card has beed registered.
     *
     * @param DateTime $registerDate Date when the card has beed registered
     *
     * @return CreditCard
     */
    public function setRegisterDate(DateTime $registerDate)
    {
        $this->registerDate = $registerDate;

        return $this;
    }

    /**
     * Set an id of the first order made using the card.
     *
     * @param int $orderId Id of the first order made using the card
     *
     * @return getCreditCardByOrder
     */
    public function setOrderId($orderId)
    {
        $this->orderId = (int) $orderId;

        return $this;
    }

    /**
     * Return credit card which is connected with the given order id. This function is a mock and it should be overwritten in a children CreditCard class.
     *
     * @param int $orderId Order id
     *
     * @return getCreditCardByOrder
     */
    public static function getCreditCardByOrder($orderId)
    {
        $card = new static(null, null);
        $card->setOrderId($orderId);

        return $card;
    }

    /**
     * Generate a new user identificator which can be assigned to a registered card during a first transaction.
     *
     * @return string
     */
    public function generateUserId()
    {
        return self::generateNewUserId();
    }

    /**
     * Generate a new user identificator which can be assigned to a registered card during a first transaction.
     *
     * @return string
     */
    public static function generateNewUserId()
    {
        $microtime = ''.self::generateTimeValue();
        $md5 = md5($microtime);

        $mtRand = self::generateRandomValue();

        $md5Substr = substr($md5, $mtRand, 21);

        $a = substr($md5Substr, 0, 6);
        $b = substr($md5Substr, 6, 5);
        $c = substr($md5Substr, 11, 6);
        $d = substr($md5Substr, 17, 4);

        return "{$a}-{$b}-{$c}-{$d}";
    }

    /**
     * Return current Unix timestamp with microseconds.
     *
     * @codeCoverageIgnore
     *
     * @return int
     */
    protected static function generateTimeValue()
    {
        return (int) microtime(true);
    }

    /**
     * Return a random value between 0 and 11.
     *
     * @codeCoverageIgnore
     *
     * @return int
     */
    protected static function generateRandomValue()
    {
        return mt_rand(0, 11);
    }
}
