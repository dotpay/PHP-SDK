<?php

namespace Dotpay\Html;

class Img extends Single {
    public function __construct($src) {
        parent::__construct('img');
        $this->setSrc($src);
    }
    
    public function getSrc() {
        return $this->getAttribute('src');
    }
    
    public function getAlt() {
        return $this->getAttribute('alt');
    }
    
    public function setSrc($src) {
        return $this->setAttribute('src', $src);
    }
    
    public function setAlt($alt) {
        return $this->setAttribute('alt', $alt);
    }
}