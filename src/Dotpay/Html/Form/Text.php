<?php

namespace Dotpay\Html\Form;

class Text extends Input {
    public function __construct($name = null, $value = '') {
        parent::__construct('text', $name, $value);
    }
}