<?php

namespace Dotpay\Validator;

class BNumber implements IValidate {
    public static function validate($value) {
        return (bool)preg_match('/^[0-9]+[0-9]*([A-Za-z]?|(\/[0-9]+\s?[A-Za-z0-9]*)?)$/', $value);
    }
}

