<?php

namespace Dotpay\Model;

use Dotpay\Validator\Url;
use Dotpay\Exception\BadParameter\UrlException;

class CardBrand {
    private $name;
    private $codeName;
    private $image;
    
    public function getName() {
        return $this->name;
    }
    
    public function getCodeName() {
        return $this->codeName;
    }

    public function getImage() {
        return $this->image;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    
    public function setCodeName($codeName) {
        $this->codeName = $codeName;
        return $this;
    }

    public function setImage($image) {
        if(!Url::validate($image))
            throw new UrlException($image);
        $this->image = $image;
        return $this;
    }

    public function __construct($name, $image, $codeName = null) {
        $this->setName($name);
        $this->setImage($image);
        $this->setCodeName($codeName);
    }
}