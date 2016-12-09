<?php

namespace Dotpay\Model;

use Dotpay\Validator\CardMask;
use Dotpay\Validator\Url;
use Dotpay\Exception\BadParameter\CardMaskException;
use Dotpay\Exception\BadParameter\UrlException;

class CreditCard {
    private $id;
    private $mask;
    private $brand;
    private $userId;
    private $cardId;
    private $href;

    public function __construct($id, $userId) {
        $this->setId($id);
        $this->setUserId($userId);
    }
    
    public function getId() {
        return $this->id;
    }

    public function getMask() {
        return $this->mask;
    }

    public function getBrand() {
        return $this->brand;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getCardId() {
        return $this->cardId;
    }
    
    public function getHref() {
        return $this->href;
    }
    
    public function isRegistered() {
        return !($this->getCardId() === null ||
                $this->getBrand() === null ||
                $this->getMask() === null);
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setMask($mask) {
        $mask = str_replace(' ', '-', strtoupper($mask));
        if(!CardMask::validate($mask))
            throw new CardMaskException($mask);
        $this->mask = $mask;
        return $this;
    }

    public function setBrand(CardBrand $brand) {
        $this->brand = $brand;
        return $this;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
        return $this;
    }

    public function setCardId($cardId) {
        $this->cardId = $cardId;
        return $this;
    }
    
    public function setHref($href) {
        if(!Url::validate($href))
            throw new UrlException($href);
        $this->href = $href;
        return $this;
    }
    
    public function generateUserId() {
        $microtime = '' . $this->generateTimeValue();
        $md5 = md5($microtime);

        $mtRand = $this->generateRandomValue();

        $md5Substr = substr($md5, $mtRand, 21);

        $a = substr($md5Substr, 0, 6);
        $b = substr($md5Substr, 6, 5);
        $c = substr($md5Substr, 11, 6);
        $d = substr($md5Substr, 17, 4);

        return $this->setUserId("{$a}-{$b}-{$c}-{$d}");
    }
    
    /**
     * @codeCoverageIgnore
     */
    protected function generateTimeValue() {
        return microtime(true);
    }
    
    /**
     * @codeCoverageIgnore
     */
    protected function generateRandomValue() {
        return mt_rand(0, 11);
    }
}

?>
