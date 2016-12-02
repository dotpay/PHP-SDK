<?php

namespace Dotpay\Validator;

class ChannelId implements IValidate {
    public static function validate($value) {
        return (bool)preg_match("/^\d{1,5}$/", $value);
    }
}
