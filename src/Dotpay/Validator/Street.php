<?php

namespace Dotpay\Validator;

class Street implements IValidate {
    public static function validate($value) {
        return (bool)preg_match('/^[A-Za-z0-9\.\s\-\/]{2,}$/', $value);
    }
}

