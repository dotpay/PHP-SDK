<?php

namespace Dotpay\Html;

abstract class Single extends Element {
    public function __toString() {
        return '<'.$this->getType().$this->getAttributeList().' />';
    }
}