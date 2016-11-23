<?php

namespace Dotpay\Model;

class CardBrand {
    private $name;
    private $image;
    
    public function getName() {
        return $this->name;
    }

    public function getImage() {
        return $this->image;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function setImage($image) {
        $this->image = $image;
        return $this;
    }

    public function __construct($name, $image) {
        $this->setName($name);
        $this->setImage($image);
    }
}