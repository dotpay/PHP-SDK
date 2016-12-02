<?php

namespace Dotpay\Model;

use Dotpay\Validator\Name;
use Dotpay\Validator\Email;
use Dotpay\Exception\BadParameter\FirstnameException;
use Dotpay\Exception\BadParameter\LastnameException;
use Dotpay\Exception\BadParameter\EmailException;

class Payer {
    private $firstName;
    private $lastName;
    private $email;
    
    public function __construct($email, $firstName, $lastName) {
        $this->setEmail($email);
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
    }
    
    public function getFirstName() {
        return $this->firstName;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setFirstName($firstName) {
        if(!Name::validate($firstName))
            throw new FirstnameException($firstName);
        $this->firstName = $firstName;
        return $this;
    }

    public function setLastName($lastName) {
        if(!Name::validate($lastName))
            throw new LastnameException($lastName);
        $this->lastName = $lastName;
        return $this;
    }

    public function setEmail($email) {
        if(!Email::validate($email))
            throw new EmailException($email);
        $this->email = $email;
        return $this;
    }
}