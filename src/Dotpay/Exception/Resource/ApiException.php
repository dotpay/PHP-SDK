<?php

namespace Dotpay\Exception\Resource;

class ApiException extends \RuntimeException {
    private $apiCode;
    
    public function setApiCode($code) {
        $this->apiCode = $code;
        return $this;
    }
    
    public function getApiCode() {
        return $this->apiCode;
    }
}

?>
