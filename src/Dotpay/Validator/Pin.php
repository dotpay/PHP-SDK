<?php

namespace Dotpay\Validator;

class Pin implements IValidate {
    public static function validate($value) {
        return (bool)preg_match('/^[A-Za-z0-9]{32}$/', $value);
    }
}