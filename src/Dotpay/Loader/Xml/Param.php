<?php

namespace Dotpay\Loader\Xml;

class Param {
    private $className;
    private $name;
    private $value;
    private $storedValue;
    
    public function __construct($className = '', $name = '', $value = '') {
        $this->className = (string)$className;
        $this->name = (string)$name;
        $this->value = (string)$value;
    }
    
    public function getClassName() {
        return $this->className;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getValue() {
        return $this->value;
    }
    
    public function getStoredValue() {
        if(!empty($this->storedValue))
            return $this->storedValue;
        else if(!empty($this->getValue()))
            return $this->getValue();
        else
            return null;
    }
    
    public function setStoredValue($value) {
        $this->storedValue = $value;
        return $this;
    }
    
    public function getXml() {
        $element = '<param';
        if(!empty($this->getClassName()))
            $element .= ' class=\''.$this->getClassName().'\'';
        if(!empty($this->getName()))
            $element .= ' name=\''.$this->getName().'\'';
        if(!empty($this->getValue()))
            $element .= ' value=\''.$this->getValue().'\'';
        $element .= ' />';
        return $element;
    }
}