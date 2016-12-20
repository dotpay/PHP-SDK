<?php

namespace Dotpay\Validator;

class OpNumber implements IValidate {
    public static function validate($value) {
        return (bool)preg_match('/^M\d{4}\-\d{4}$/', $value);
    }
}