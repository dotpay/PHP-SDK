<?php

namespace Dotpay\Html\Container;

class Span extends Container {
    public function __construct($children = []) {
        parent::__construct('span', $children);
    }
}