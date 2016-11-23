<?php

namespace Dotpay\Html\Container;

class A extends Container {
    public function __construct($href = null, $children = []) {
        parent::__construct('a', $children);
        $this->setHref($href);
    }
    
    public function getHref() {
        return $this->getAttribute('href');
    }
    
    public function setHref($href) {
        return $this->setAttribute('href', $href);
    }
}