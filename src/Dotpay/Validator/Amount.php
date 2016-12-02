<?php

namespace Dotpay\Validator;

class Amount implements IValidate {
    public static function validate($value) {
        return (bool)preg_match('/^\-?\d{1,}(\.\d{1,4})?$/', $value);
    }
}