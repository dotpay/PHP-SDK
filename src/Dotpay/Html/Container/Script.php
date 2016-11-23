<?php

namespace Dotpay\Html\Container;

class Script extends Container {
    public function __construct($children = [], $src = null) {
        parent::__construct('script', $children);
        $this->setAttribute('type', 'text/javascript');
        if(!empty($src))
            $this->setSrc($src);
    }
    
    public function setSrc($src) {
        $this->setAttribute('src', $src);
    }
}