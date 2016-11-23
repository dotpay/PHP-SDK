<?php

namespace Dotpay\Html;

abstract class Element extends Node {
    private $type;
    
    public function getType() {
        return $this->type;
    }
    
    public function __construct($type = '', $name=null) {
        $this->setType($type);
        if($name!==null)
            $this->setName($name);
    }
    
    public function getName() {
        return $this->getAttribute('name');
    }
    
    public function setName($name) {
        return $this->setAttribute('name', $name);
    }
    
    public function getClass() {
        return $this->getAttribute('class');
    }
    
    public function setClass($className) {
        return $this->setAttribute('class', $className);
    }
    
    public function getData($name) {
        return $this->getAttribute('data-'.$name);
    }
    
    public function setData($name, $value) {
        return $this->setAttribute('data-'.$name, $value);
    }
    
    public function __toString() {
        return parent::__toString();
    }
    
    private function setType($type) {
        $this->type = $type;
    }
}
