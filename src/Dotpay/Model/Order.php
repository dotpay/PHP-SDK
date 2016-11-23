<?php

namespace Dotpay\Model;

use Dotpay\Exception\BadParameter\AmountException;
use Dotpay\Exception\BadParameter\CurrencyException;

class Order {
    private $id;
    private $amount;
    private $currency;
    private $reference;

    const CURRENCIES = Configuration::currencies;
    
    public function __construct($id, $amount, $currency) {
        $this->setId($id);
        $this->setAmount($amount);
        $this->setCurrency($currency);
    }
    
    public function getId() {
        return $this->id;
    }

    public function getAmount() {
        return $this->amount;
    }

    public function getCurrency() {
        return $this->currency;
    }

    public function getReference() {
        return $this->reference;
    }

    public function setId($id) {
        $this->id = $id;
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
        if(!in_array($currency, self::CURRENCIES))
            throw new CurrencyException($currency);
        $this->currency = $currency;
        return $this;
    }

    public function setReference($reference) {
        $this->reference = $reference;
        return $this;
    }
}
