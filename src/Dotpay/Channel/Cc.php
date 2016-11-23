<?php

namespace Dotpay\Channel;

use Dotpay\Model\Configuration;
use Dotpay\Model\Transaction;
use Dotpay\Resource\Payment as PaymentResource;

class Cc extends Channel {
    public function __construct(Configuration $config, Transaction $transaction, PaymentResource $paymentResource) {
        parent::__construct(Configuration::ccChannel, 'cc', $config, $transaction, $paymentResource);
    }
    
    public function getVisibility() {
        return $this->config->getCcVisible() && 
               !($this->config->getPvCorrect() || 
                 $this->config->isCurrencyForPv(
                    $this->transaction->getPayment()->getOrder()->getCurrency()
                 ));
    }
}

?>
