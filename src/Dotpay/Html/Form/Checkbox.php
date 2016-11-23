<?php

namespace Dotpay\Html\Form;

class Checkbox extends Input {
    public function __construct($name = null, $value = '') {
        parent::__construct('checkbox', $name, $value);
    }
}