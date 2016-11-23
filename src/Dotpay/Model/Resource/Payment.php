<?php

namespace Dotpay\Resource\Model;

use Dotpay\Model\Configuration;
use Dotpay\Validator\Url;
use Dotpay\Validator\Id;
use Dotpay\Exception\BadParameter\UrlExceptionException;
use Dotpay\Exception\BadParameter\IdException;
use Dotpay\Exception\BadParameter\AmountException;
use Dotpay\Exception\BadParameter\CurrencyException;

class Payment {
    private $url;
    private $number;
    private $creationTime;
    private $type;
    private $status;
    private $amount;
    private $currency;
    private $originalCurrency;
    private $originalAmount;
    private $accountId;
    private $relatedOperation;
    private $description;
    private $control;
    private $payer;
    private $paymentMethod;
    
    public function __construct($type, $number) {
        $this->type = $type;
        $this->number = $number;
    }
    
    public function getUrl() {
        return $this->url;
    }

    public function getNumber() {
        return $this->number;
    }

    public function getCreationTime() {
        return $this->creationTime;
    }

    public function getType() {
        return $this->type;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getAmount() {
        return $this->amount;
    }

    public function getCurrency() {
        return $this->currency;
    }

    public function getOriginalCurrency() {
        return $this->originalCurrency;
    }

    public function getOriginalAmount() {
        return $this->originalAmount;
    }

    public function getAccountId() {
        return $this->accountId;
    }

    public function getRelatedOperation() {
        return $this->relatedOperation;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getControl() {
        return $this->control;
    }

    public function getPayer() {
        return $this->payer;
    }

    public function getPaymentMethod() {
        return $this->paymentMethod;
    }

    public function setUrl($url) {
        if(!Url::validate($url))
            throw new UrlExceptionException($url);
        $this->url = $url;
        return $this;
    }

    public function setNumber($number) {
        $this->number = $number;
        return $this;
    }

    public function setCreationTime($creationTime) {
        $this->creationTime = $creationTime;
        return $this;
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    public function setStatus($status) {
        $this->status = $status;
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

    public function setOriginalCurrency($originalCurrency) {
        $originalCurrency = strtoupper($originalCurrency);
        if(!in_array($originalCurrency, Configuration::CURRENCIES))
            throw new CurrencyException($originalCurrency);
        $this->originalCurrenc = $originalCurrency;
        return $this;
    }

    public function setOriginalAmount($originalAmount) {
        if(empty($originalAmount))
            throw new AmountException($originalAmount);
        $this->originalAmount = $originalAmount;
        return $this;
    }

    public function setAccountId($accountId) {
        if(!Id::validate($url))
            throw new IdException($url);
        $this->accountId = $accountId;
        return $this;
    }

    public function setRelatedOperation($relatedOperation) {
        $this->relatedOperation = $relatedOperation;
        return $this;
    }

    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    public function setControl($control) {
        $this->control = $control;
        return $this;
    }

    public function setPayer(Payer $payer) {
        $this->payer = $payer;
        return $this;
    }

    public function setPaymentMethod(PaymentMethod $paymentMethod) {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }
}