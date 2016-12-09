<?php

namespace Dotpay\Model;

use Dotpay\Exception\BadParameter\CurrencyException;

class Payout {
    private $currency;
    private $transfers;
    
    public function __construct($currency) {
        $this->setCurrency($currency);
    }
    
    public function getCurrency() {
        return $this->currency;
    }
    
    public function getTransfers() {
        return $this->transfers;
    }
    
    public function setCurrency($currency) {
        $currency = strtoupper($currency);
        if(!in_array($currency, Configuration::CURRENCIES))
            throw new CurrencyException($currency);
        $this->currency = $currency;
        return $this;
    }

    public function addTransfer(Transfer $transfer) {
        $this->transfers[] = $transfer;
        return $this;
    }
}

?>