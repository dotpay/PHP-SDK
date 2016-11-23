<?php

namespace Dotpay\Html\Form;

use Dotpay\Html\Node;

class Label extends Node {
    private $element;
    private $llabel;
    private $rlabel;
    
    public function __construct(Node $element, $llabel = '', $rlabel = '') {
        $this->element = $element;
        $this->setLLabel($llabel);
        $this->setRLabel($rlabel);
    }
    
    public function getElement() {
        return $this->element;
    }
    
    public function getLLabel() {
        return $this->llabel;
    }
    
    public function getRLabel() {
        return $this->rlabel;
    }
    
    public function setLLabel($label) {
        $this->llabel = $label;
    }
    
    public function setRLabel($label) {
        $this->rlabel = $label;
    }
    
    public function __toString() {
        return '<label'.
                $this->getAttributeList().
                '>'.
                $this->getLLabel().
                (string)$this->getElement().
                $this->getRLabel().
                '</label>';
    }
}