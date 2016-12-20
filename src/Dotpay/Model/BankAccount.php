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
        if(empty($name))
            $name = null;
        $this->name = $name;
        return $this;
    }

    public function setNumber($number) {
        if(preg_match('/^\d{26}$/', trim($number)) === 1)
            $number = 'PL'.$number;
        if(!empty($number) && !BankNumber::validate($number))
            throw new BankNumberException($number);
        if(empty($number))
            $number = null;
        $this->number = $number;
        return $this;
    }
}