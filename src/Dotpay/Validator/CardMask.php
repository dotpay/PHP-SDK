<?php

namespace Dotpay\Validator;

class CardMask implements IValidate {
    public static function validate($value) {
        return (bool)preg_match('/^(X{4}\-){3}[0-9]{4}$/', $value);
    }
}

