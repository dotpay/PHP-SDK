<?php

namespace Dotpay\Html\Container;

use Dotpay\Html\Element;
use Dotpay\Html\Node;
use Dotpay\Html\PlainText;

abstract class Container extends Element {
    private $children = [];
    
    public function __construct($type, $children = []) {
        parent::__construct($type);
        if($children instanceof Node)
            $children = [$children];
        else if($children === null ||
                is_scalar($children) ||
                is_callable([$children, '__toString'])) {
            $children = [new PlainText($children)];
        }
        $this->setChildren($children);
    }
    
    public function getChildren() {
        return $this->children;
    }
    
    public function addChild(Node $child) {
        $this->children[] = $child;
    }
    
    public function setChildren(array $children) {
        $this->children = [];
        foreach ($children as $child) {
            if($child instanceof Node)
                $this->addChild($child);
        }
    }
    
    public function removeChild(Node $child) {
        foreach ($this->getChildren() as $index => $oneChild) {
            if($oneChild === $child) {
                array_splice($this->children, $index, 1);
                break;
            }
        }
        return $this;
    }
    
    public function __toString() {
        $text = '<'.$this->getType().$this->getAttributeList().'>';
        foreach($this->getChildren() as $child) {
            $text .= (string)$child;
        }
        $text .= '</'.$this->getType().'>';
        return $text;
    }
}
