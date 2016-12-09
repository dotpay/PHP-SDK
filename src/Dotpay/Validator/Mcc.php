<?php

namespace Dotpay\Validator;

class Mcc implements IValidate {
    public static function validate($value) {
        return (bool)preg_match('/^\d{4}$/', $value);
    }
}