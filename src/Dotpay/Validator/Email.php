<?php

namespace Dotpay\Validator;

class Email implements IValidate {
    public static function validate($value) {
        return (bool)filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}

