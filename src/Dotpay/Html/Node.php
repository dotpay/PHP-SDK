<?php

namespace Dotpay\Html;

abstract class Node {
    private $attributes = [];
    
    public function getAttribute($name) {
        return isset($this->attributes[$name])?$this->attributes[$name]:null;
    }
    
    public function setAttribute($name, $value) {
        $this->attributes[$name] = $value;
        return $this;
    }
    
    public function removeAttribute($name) {
        if(isset($this->attributes[$name]))
            unset($this->attributes[$name]);
        return $this;
    }
    
    public function getAttributes() {
        return $this->attributes;
    }
    
    protected function getAttributeList() {
        $html = '';
        foreach($this->getAttributes() as $name => $value)
            $html .= ' '.$name.'=\''.$value.'\'';
        return $html;
    }
    
    public function __toString() {
        return '';
    }
}