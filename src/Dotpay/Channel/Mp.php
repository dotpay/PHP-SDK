<?php

namespace Dotpay\Channel;

use Dotpay\Model\Configuration;
use Dotpay\Model\Transaction;
use Dotpay\Resource\Payment as PaymentResource;

class Mp extends Channel {
    public function __construct(Configuration $config, Transaction $transaction, PaymentResource $paymentResource) {
        parent::__construct(Configuration::mpChannel, 'mp', $config, $transaction, $paymentResource);
    }
    
    public function getVisibility() {
        return $this->config->getMpVisible();
    }
}

?>
