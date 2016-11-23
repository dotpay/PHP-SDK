<?php

namespace Dotpay\Html\Form;

use Dotpay\Html\Container\Container;
use Dotpay\Html\PlainText;

class Option extends Container {
    public function __construct($text, $value = null) {
        parent::__construct('option');
        $this->setText($text);
        if(!empty($value))
            $this->setValue($value);
    }
    
    public function getValue() {
        return $this->getAttribute('value');
    }
    
    public function getText() {
        $children = $this->getChildren();
        return $children[0];
    }
    
    public function setValue($value) {
        return $this->setAttribute('value', $value);
    }
    
    public function setSelected($mode = true) {
        if($mode)
            return $this->setAttribute('selected', 'selected');
        else
            return $this->removeAttribute ('selected');
    }
    
    public function isSelected() {
        return (bool)$this->getAttribute('selected');
    }
    
    public function setText($text) {
        if(!empty($text)) {
            if(!($text instanceof PlainText))
                $text = new PlainText($text);
            $this->setChildren([$text]);
        }
        return $this;
    }
}