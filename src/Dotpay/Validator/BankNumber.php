<?php

namespace Dotpay\Validator;

use Dotpay\Tool\IBAN;

/**
 * @todo Correct validation
 */
class BankNumber implements IValidate {
    public static function validate($value) {
        try {
            IBAN::createFromString($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

