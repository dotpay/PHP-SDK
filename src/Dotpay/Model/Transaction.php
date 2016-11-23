<?php

namespace Dotpay\Model;

use Dotpay\Model\Payment;

class Transaction {
    private $payment;
    private $backUrl;
    private $confirmUrl;
    
    public function __construct(Payment $payment) {
        $this->payment = $payment;
    }
    
    public function getPayment() {
        return $this->payment;
    }
    
    public function getBackUrl() {
        return $this->backUrl;
    }

    public function getConfirmUrl() {
        return $this->confirmUrl;
    }

    public function setBackUrl($backUrl) {
        $this->backUrl = $backUrl;
        return $this;
    }

    public function setConfirmUrl($confirmUrl) {
        $this->confirmUrl = $confirmUrl;
        return $this;
    }
}

?>