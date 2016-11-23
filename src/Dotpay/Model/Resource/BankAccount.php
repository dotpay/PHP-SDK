<?php

namespace Dotpay\Resource\Model;

use Dotpay\Validator\BankNumber;

class BankAccount {
    private $name;
    private $number;
    
    public function __construct($name = null, $number = null) {
        $this->name = $name;
        $this->number = $number;
    }
    
    public function getName() {
        return $this->name;
    }

    public function getNumber() {
        return $this->number;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function setNumber($number) {
        if($number !== null && !CardMask::validate($mask))
            throw new CardMaskException($mask);
        $this->number = $number;
        return $this;
    }
}