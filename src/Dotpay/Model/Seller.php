<?php

namespace Dotpay\Model;

use Dotpay\Validator\Id;
use Dotpay\Validator\Pin;
use Dotpay\Validator\Email;
use Dotpay\Validator\Username;
use Dotpay\Exception\BadParameter\IdException;
use Dotpay\Exception\BadParameter\PinException;
use Dotpay\Exception\BadParameter\UsernameException;
use Dotpay\Exception\BadParameter\EmailException;

class Seller {
    private $id;
    private $pin;
    private $username;
    private $password;
    private $info;
    private $email;

    public function __construct($id, $pin) {
        $this->setId($id);
        $this->setPin($pin);
    }

    public function getId() {
        return $this->id;	
    }

    public function getPin() {
        return $this->pin;	
    }

    public function getUsername() {
        return $this->username;	
    }

    public function getPassword() {
        return $this->password;	
    }

    public function getInfo() {
        return $this->info;	
    }

    public function getEmail() {
        return $this->email;	
    }

    public function hasAccessToApi() {
        return (!empty($this->username) && !empty($this->password));
    }

    public function setId($id) {
        if(!Id::validate($id))
            throw new IdException($id);
        $this->id = $id;
        return $this;
    }

    public function setPin($pin) {
        if(!Pin::validate($pin))
            throw new PinException($pin);
        $this->pin = $pin;
        return $this;
    }

    public function setUsername($username) {
        if(!Username::validate($username))
            throw new UsernameException($username);
        $this->username = $username;
        return $this;
    }

    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }
    
    public function setInfo($info) {
        $this->info = $info;
        return $this;
    }

    public function setEmail($email) {
        if(!Email::validate($email))
            throw new EmailException($email);
        $this->email = $email;
        return $this;
    }
}