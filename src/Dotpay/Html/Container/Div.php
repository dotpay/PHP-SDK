<?php

namespace Dotpay\Html\Container;

class Div extends Container {
    public function __construct($children = []) {
        parent::__construct('div', $children);
    }
}