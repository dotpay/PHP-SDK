<?php

namespace Dotpay\Model;

use Dotpay\Validator\BankNumber;
use Dotpay\Exception\BadParameter\BankNumberException;

class BankAccount {
    private $name;
    private $number;
    
    public function __construct($name = null, $number = null) {
        $this->setName($name);
        $this->setNumber($number);
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
        if($number !== null && !BankNumber::validate($number))
            throw new BankNumberException($number);
        $this->number = $number;
        return $this;
    }
}