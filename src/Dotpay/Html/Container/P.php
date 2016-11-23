<?php

namespace Dotpay\Html\Container;

class P extends Container {
    public function __construct($children = []) {
        parent::__construct('p', $children);
    }
}