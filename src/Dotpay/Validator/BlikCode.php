<?php

namespace Dotpay\Validator;

class BlikCode implements IValidate {
    public static function validate($value) {
        return (bool)preg_match("/^\d{6}$/", $value);
    }
}
