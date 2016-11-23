<?php

namespace Dotpay\Validator;

class Name implements IValidate {
    public static function validate($value) {
        return (strlen($value) > 0) && (preg_match('/^[^\~\@#\$%\^&\*\(\)_+\|\}\{":\?><\/\.,\';\]\[=`\\0123456789]+$/', $value) === 1);
    }
}