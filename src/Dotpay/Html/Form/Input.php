<?php

namespace Dotpay\Html\Form;

use Dotpay\Html\Single;

class Input extends Single {
    public function __construct($type, $name = '', $value = '') {
        $this->setInputType($type);
        parent::__construct('input', $name);
        if(!empty($value))
            $this->setValue($value);
    }
    
    public function getInputType() {
        return $this->getAttribute('type');
    }
    
    public function getValue() {
        return $this->getAttribute('value');
    }
    
    public function setInputType($type) {
        return $this->setAttribute('type', $type);
    }
    
    public function setValue($value) {
        return $this->setAttribute('value', $value);
    }
    
    
}
