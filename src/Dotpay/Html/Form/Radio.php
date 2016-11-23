<?php

namespace Dotpay\Html\Form;

class Radio extends Input {
    public function __construct($name = null, $value = '') {
        parent::__construct('radio', $name, $value);
    }
}