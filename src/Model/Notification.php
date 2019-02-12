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

use Dotpay\Provider\NotificationProviderInterface;
use Dotpay\Validator\Email;
use Dotpay\Validator\ChannelId;
use Dotpay\Exception\BadParameter\EmailException;
use Dotpay\Exception\BadParameter\ChannelIdException;

/**
 * Informations about a notification from Dotpay server.
 */
class Notification
{
    /**
     * @var Operation Operation object with details of operation which relates the notification
     */
    private $operation;

    /**
     * @var string Email of a seller
     */
    private $shopEmail = '';

    /**
     * @var string Name of a shop
     */
    private $shopName = '';

    /**
     * @var int Id of used payment channel
     */
    private $channelId;

    /**
     * @var string Codename of a country of the payment instrument from which payment was made
     */
    private $channelCountry = '';

    /**
     * @var string Codename of a country resulting from IP address from which the payment was made
     */
    private $ipCountry = '';

    /**
     * @var CreditCard|null CreditCard object if payment was realize by credit card and this information is allowed to send
     */
    private $creditCard = null;

    /**
     * @var string Checksum of a Dotpay notification
     */
    private $signature = '';

    /**
     * Create the model based on data provided from shop.
     *
     * @param NotificationProviderInterface $provider Provider which contains data from shop application
     *
     * @return Notification
     */
    public static function createFromData(NotificationProviderInterface $provider)
    {
        $notification = new static(
            $provider->getOperation(),
            $provider->getChannelId()
        );
        if ($provider->getShopEmail() !== null) {
            $notification->setShopEmail($provider->getShopEmail());
        }
        if ($provider->getShopName() !== null) {
            $notification->setShopName($provider->getShopName());
        }
        if ($provider->getChannelCountry() !== null) {
            $notification->setChannelCountry($provider->getChannelCountry());
        }
        if ($provider->getIpCountry() !== null) {
            $notification->setIpCountry($provider->getIpCountry());
        }
        $notification->setSignature($provider->getSignature());

        return $notification;
    }

    /**
     * Initialize the model.
     *
     * @param Operation $operation Details of operation which relates the notification
     * @param int       $channel   Id of used payment channel
     */
    public function __construct(Operation $operation, $channel = null)
    {
        $this->setOperation($operation);
        if ($channel != null) {
            $this->setChannelId($channel);
        }
    }

    /**
     * Return an Operation object with details of operation which relates the notification.
     *
     * @return Operation
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * Return an email of a seller.
     *
     * @return string
     */
    public function getShopEmail()
    {
        return $this->shopEmail;
    }

    /**
     * Return a name of a shop.
     *
     * @return string
     */
    public function getShopName()
    {
        return $this->shopName;
    }

    /**
     * Return an id of used payment channel.
     *
     * @return int
     */
    public function getChannelId()
    {
        return $this->channelId;
    }

    /**
     * Return a codename of a country of the payment instrument from which payment was made.
     *
     * @return string
     */
    public function getChannelCountry()
    {
        return $this->channelCountry;
    }

    /**
     * Return a codename of a country resulting from IP address from which the payment was made.
     *
     * @return string
     */
    public function getIpCountry()
    {
        return $this->ipCountry;
    }

    /**
     * Return a CreditCard object if payment was realize by credit card and this information is allowed to send.
     *
     * @return CreditCard|null
     */
    public function getCreditCard()
    {
        return $this->creditCard;
    }

    /**
     * Return a checksum of a Dotpay notification.
     *
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * Calculate a signature based on data from the notification and the given seller pin.
     *
     * @param string $pin Seller pin
     *
     * @return string
     */

    public function calculateSignature($pin)
    {
        $sign =
            $pin.
            $this->getOperation()->getAccountId().
            $this->getOperation()->getNumber().
            $this->getOperation()->getType().
            $this->getOperation()->getStatus().
            number_format($this->getOperation()->getAmount(),2, '.', '').
            $this->getOperation()->getCurrency().
            (is_null($this->getOperation()->getWithdrawalAmount()) ? null : number_format($this->getOperation()->getWithdrawalAmount(),2, '.', '')).
            (is_null($this->getOperation()->getCommissionAmount()) ? null : number_format($this->getOperation()->getCommissionAmount(),2, '.', '')).
            $this->getOperation()->isCompletedString().
            number_format($this->getOperation()->getOriginalAmount(),2, '.', '').
            $this->getOperation()->getOriginalCurrency().
            $this->getOperation()->getDateTime()->format('Y-m-d H:i:s').
            $this->getOperation()->getRelatedNumber().
            $this->getOperation()->getControl().
            $this->getOperation()->getDescription().
            $this->getOperation()->getPayer()->getEmail().
            $this->getShopName().
            $this->getShopEmail();
        if ($this->getCreditCard() !== null) {
            $sign .=
                $this->getCreditCard()->getIssuerId().
                $this->getCreditCard()->getMask().
                $this->getCreditCard()->getBrand()->getCodeName().
                $this->getCreditCard()->getBrand()->getName().
                $this->getCreditCard()->getCardId();
        }
        $sign .=
            $this->getChannelId().
            $this->getChannelCountry().
            $this->getIpCountry();
        return hash('sha256', $sign);
    }

    /**
     * Set an Operation object with details of operation which relates the notification.
     *
     * @param Operation $operation Operation object with details of operation which relates the notification
     *
     * @return Notification
     */
    public function setOperation(Operation $operation)
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * Set an email of a seller.
     *
     * @param string $shopEmail Email of a seller
     *
     * @return Notification
     *
     * @throws EmailException Thrown when the given seller email is incorrect
     */
    public function setShopEmail($shopEmail)
    {
        if (!Email::validate($shopEmail)) {
            throw new EmailException($shopEmail);
        }
        $this->shopEmail = $shopEmail;

        return $this;
    }

    /**
     * Set a name of a shop.
     *
     * @param string $shopName Name of a shop
     *
     * @return Notification
     */
    public function setShopName($shopName)
    {
        $this->shopName = $shopName;

        return $this;
    }

    /**
     * Set an id of used payment channel.
     *
     * @param int $channelId Id of used payment channel
     *
     * @return Notification
     *
     * @throws ChannelIdException Thrown when the given channel id is incorrect
     */
    public function setChannelId($channelId)
    {
        if (!ChannelId::validate($channelId)) {
            throw new ChannelIdException($channelId);
        }
        $this->channelId = $channelId;

        return $this;
    }

    /**
     * Set a codename of a country of the payment instrument from which payment was made.
     *
     * @param string $channelCountry Codename of a country of the payment instrument from which payment was made
     *
     * @return Notification
     */
    public function setChannelCountry($channelCountry)
    {
        $this->channelCountry = $channelCountry;

        return $this;
    }

    /**
     * Set a codename of a country resulting from IP address from which the payment was made.
     *
     * @param string $ipCountry Codename of a country resulting from IP address from which the payment was made
     *
     * @return Notification
     */
    public function setIpCountry($ipCountry)
    {
        $this->ipCountry = $ipCountry;

        return $this;
    }

    /**
     * Set a CreditCard object if payment was realize by credit card and this information is allowed to send.
     *
     * @param CreditCard $creditCard CreditCard object if payment was realize by credit card and this information is allowed to send
     *
     * @return Notification
     */
    public function setCreditCard(CreditCard $creditCard)
    {
        $this->creditCard = $creditCard;

        return $this;
    }

    /**
     * Set a checksum of a Dotpay notification.
     *
     * @param string $signature Checksum of a Dotpay notification
     *
     * @return Notification
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;

        return $this;
    }
}
