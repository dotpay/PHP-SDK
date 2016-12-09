<?php

namespace Dotpay\Model;

use Dotpay\Validator\Id;
use Dotpay\Validator\Mcc;
use Dotpay\Validator\Url;
use Dotpay\Validator\Pin;
use Dotpay\Exception\BadParameter\IdException;
use Dotpay\Exception\BadParameter\MccException;
use Dotpay\Exception\BadParameter\UrlException;
use Dotpay\Exception\BadParameter\PinException;

class Account {
    private $id;
    private $status;
    private $name;
    private $mcc;
    private $urlc;
    private $blockExternalUrlc;
    private $pin;
    
    public function __construct($id) {
        $this->setId($id);
    }
    
    public function getId() {
        return $this->id;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getName() {
        return $this->name;
    }

    public function getMcc() {
        return $this->mcc;
    }

    public function getUrlc() {
        return $this->urlc;
    }

    public function getBlockExternalUrlc() {
        return $this->blockExternalUrlc;
    }

    public function getPin() {
        return $this->pin;
    }

    public function setId($id) {
        if(!Id::validate($id))
            throw new IdException($id);
        $this->id = $id;
        return $this;
    }

    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function setMcc($mcc) {
        if(!empty($mcc) && !Mcc::validate($mcc))
            throw new MccException($mcc);
        $this->mcc = $mcc;
        return $this;
    }

    public function setUrlc($urlc) {
        if(!empty($urlc) && !Url::validate($urlc))
            throw new UrlException($urlc);
        $this->urlc = $urlc;
        return $this;
    }

    public function setBlockExternalUrlc($blockExternalUrlc) {
        $this->blockExternalUrlc = ($blockExternalUrlc == 'false');
        return $this;
    }

    public function setPin($pin) {
        if(!Pin::validate($pin))
            throw new PinException($pin);
        $this->pin = $pin;
        return $this;
    }
}