<?php

namespace Dotpay\Validator;

class Ip implements IValidate {
    public static function validate($value) {
        return (bool)filter_var($value, FILTER_VALIDATE_IP);
    }
}