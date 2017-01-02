<?php

namespace Dotpay\Model;

use Dotpay\Validator\Email;
use Dotpay\Validator\ChannelId;
use Dotpay\Exception\BadParameter\EmailException;
use Dotpay\Exception\BadParameter\ChannelIdException;

class Notification {
    private $operation;
    private $email;
    private $shopEmail;
    private $shopName;
    private $channelId;
    private $channelCountry;
    private $ipCountry;
    private $creditCard;
    private $signature;
    
    public function __construct(Operation $operation, $channel) {
        $this->setOperation($operation);
        $this->setChannelId($channel);
    }
    
    public function getOperation() {
        return $this->operation;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getShopEmail() {
        return $this->shopEmail;
    }

    public function getShopName() {
        return $this->shopName;
    }

    public function getChannelId() {
        return $this->channelId;
    }

    public function getChannelCountry() {
        return $this->channelCountry;
    }

    public function getIpCountry() {
        return $this->ipCountry;
    }

    public function getCreditCard() {
        return $this->creditCard;
    }

    public function getSignature() {
        return $this->signature;
    }
    
    public function calculateSignature($pin) {
        $sign=
            $pin.
            $this->getOperation()->getAccountId().
            $this->getOperation()->getNumber().
            $this->getOperation()->getType().
            $this->getOperation()->getStatus().
            $this->getOperation()->getAmount().
            $this->getOperation()->getCurrency().
            $this->getOperation()->getWithdrawalAmount().
            $this->getOperation()->getCommissionAmount().
            $this->getOperation()->getOriginalAmount().
            $this->getOperation()->getOriginalCurrency().
            $this->getOperation()->getDateTime()->format('Y-m-d H:i:s').
            $this->getOperation()->getRelatedNumber().
            $this->getOperation()->getControl().
            $this->getOperation()->getDescription().
            $this->getEmail().
            $this->getShopName().
            $this->getShopEmail();
        if($this->getCreditCard() !== null) {
            $sign.=
                $this->getCreditCard()->getIssuerId().
                $this->getCreditCard()->getMask().
                $this->getCreditCard()->getBrand()->getCodeName().
                $this->getCreditCard()->getBrand()->getName().
                $this->getCreditCard()->getCardId();
        }
        $sign.=
            $this->getChannelId().
            $this->getChannelCountry().
            $this->getIpCountry();
        return hash('sha256', $sign);
    }

    public function setOperation(Operation $operation) {
        $this->operation = $operation;
        return $this;
    }

    public function setEmail($email) {
        if(!Email::validate($email))
            throw new EmailException($email);
        $this->email = $email;
        return $this;
    }

    public function setShopEmail($shopEmail) {
        if(!Email::validate($shopEmail))
            throw new EmailException($shopEmail);
        $this->shopEmail = $shopEmail;
        return $this;
    }

    public function setShopName($shopName) {
        $this->shopName = $shopName;
        return $this;
    }

    public function setChannelId($channelId) {
        if(!ChannelId::validate($channelId))
            throw new ChannelIdException($channelId);
        $this->channelId = $channelId;
        return $this;
    }

    public function setChannelCountry($channelCountry) {
        $this->channelCountry = $channelCountry;
        return $this;
    }

    public function setIpCountry($ipCountry) {
        $this->ipCountry = $ipCountry;
        return $this;
    }

    public function setCreditCard(CreditCard $creditCard) {
        $this->creditCard = $creditCard;
        return $this;
    }

    public function setSignature($signature) {
        $this->signature = $signature;
        return $this;
    }
}