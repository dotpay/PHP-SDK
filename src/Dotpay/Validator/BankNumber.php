<?php

namespace Dotpay\Validator;

/**
 * @todo Correct validation
 */
class BankNumber implements IValidate {
    public static function validate($value) {
        return (bool)preg_match('/^\d{26}$/', $value);
    }
}

