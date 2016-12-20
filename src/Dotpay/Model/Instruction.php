<?php

namespace Dotpay\Model;

use Dotpay\Validator\OpNumber;
use Dotpay\Validator\BankNumber;
use Dotpay\Validator\ChannelId;
use Dotpay\Exception\BadParameter\OperationNumberException;
use Dotpay\Exception\BadParameter\BankNumberException;
use Dotpay\Exception\BadParameter\AmountException;
use Dotpay\Exception\BadParameter\CurrencyException;
use Dotpay\Exception\BadParameter\ChannelIdException;

class Instruction {
    const DOTPAY_NAME = 'DOTPAY SA';
    
    const DOTPAY_STREET = 'Wielicka 72';
    
    const DOTPAY_CITY = '30-552 Kraków';
    
    private $id;
    private $orderId;
    private $number;
    private $bankAccount;
    private $amount;
    private $currency;
    private $channel;
    private $hash;
    private $isCash;
    
    public function getId() {
        return $this->id;
    }
    
    public function getOrderId() {
        return $this->orderId;
    }

    public function getNumber() {
        return $this->number;
    }

    public function getBankAccount() {
        return $this->bankAccount;
    }

    public function getAmount() {
        return $this->amount;
    }

    public function getCurrency() {
        return $this->currency;
    }

    public function getChannel() {
        return $this->channel;
    }

    public function getHash() {
        return $this->hash;
    }

    public function getIsCash() {
        return $this->isCash;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function setOrderId($orderId) {
        $this->orderId = $orderId;
        return $this;
    }

    public function setNumber($number) {
        if(!OpNumber::validate($number))
            throw new OperationNumberException($number);
        $this->number = $number;
        return $this;
    }

    public function setBankAccount($bankAccount) {
        if(preg_match('/^\d{26}$/', trim($bankAccount)) === 1)
            $bankAccount = 'PL'.$bankAccount;
        if(!empty($bankAccount) && !BankNumber::validate($bankAccount))
            throw new BankNumberException($bankAccount);
        $this->bankAccount = $bankAccount;
        return $this;
    }

    public function setAmount($amount) {
        if(empty($amount))
            throw new AmountException($amount);
        $this->amount = $amount;
        return $this;
    }

    public function setCurrency($currency) {
        $currency = strtoupper($currency);
        if(!in_array($currency, Configuration::CURRENCIES))
            throw new CurrencyException($currency);
        $this->currency = $currency;
        return $this;
    }

    public function setChannel($channel) {
        if(!ChannelId::validate($channel))
            throw new ChannelIdException($channel);
        $this->channel = $channel;
        return $this;
    }

    public function setHash($hash) {
        $this->hash = $hash;
        return $this;
    }

    public function setIsCash($isCash) {
        $this->isCash = (bool)$isCash;
        return $this;
    }


}
