<?php

namespace Dotpay\Html\Form;

use Dotpay\Html\Container\Container;
use Dotpay\Html\Form\Option;

class Select extends Container {
    private $selected;
    
    public function __construct($name = '', array $options = [], $selected = null) {
        parent::__construct('select', $options);
        if(!empty($name))
            $this->setName($name);
        if(!empty($selected))
            $this->setSelected($selected);
    }
    
    public function getSelected() {
        return $this->selected;
    }
    
    public function getOptions() {
        return $this->getChildren();
    }
    
    public function setSelected($value) {
        foreach($this->getChildren() as $option)
            if($this->checkValue($option, $value)) {
                $this->selected = $option->setSelected();
            } else {
                $option->setSelected(false);
            }
        return $this;
    }
    
    public function addOption(Option $option) {
        return $this->addChild($option);
    }
    
    public function removeOption($value) {
        foreach($this->getChildren() as $option)
            if($this->checkValue($option, $value)) {
                $this->removeChild($option);
                break;
            }
        return $this;
    }
    
    private function checkValue(Option $option, $value) {
        return $option->getValue() === $value ||
               ($option->getValue() === null && $option->getText() === $value);
    }
}