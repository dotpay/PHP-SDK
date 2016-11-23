<?php

namespace Dotpay\Validator;

class Phone implements IValidate {
    public static function validate($value) {
        return (bool)preg_match('/^\+?[\s0-9\/\-]{8,}$/', $value);
    }
}