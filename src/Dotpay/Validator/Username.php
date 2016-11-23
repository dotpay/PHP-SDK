<?php

namespace Dotpay\Validator;

class Username implements IValidate {
    public static function validate($value) {
        return ((bool)filter_var($value, FILTER_VALIDATE_EMAIL) || (preg_match('/^[A-Za-z0-9\.]{6,}$/', $value) == 1));
    }
}

