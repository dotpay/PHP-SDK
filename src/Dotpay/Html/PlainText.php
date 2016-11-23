<?php

namespace Dotpay\Html;

class PlainText extends Node {
    private $text;
    
    public function __construct($text = '') {
        $this->setText($text);
    }
    
    public function getText() {
        return $this->text;
    }
    
    public function setText($text) {
        $this->text = (string)$text;
    }


    public function __toString() {
        return $this->getText();
    }
}
