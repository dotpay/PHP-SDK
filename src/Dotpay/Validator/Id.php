<?php

namespace Dotpay\Validator;

class Id implements IValidate {
    public static function validate($value) {
        return (bool)preg_match("/^\d{5,6}$/", $value);
    }
}
