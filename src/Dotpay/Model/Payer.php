<?php

namespace Dotpay\Model;

class Payer {
    private $firstName;
    private $lastName;
    private $email;
    
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