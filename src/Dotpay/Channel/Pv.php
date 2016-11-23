<?php

namespace Dotpay\Channel;

use Dotpay\Model\Configuration;
use Dotpay\Model\Transaction;
use Dotpay\Resource\Payment as PaymentResource;

class Pv extends Channel {
    public function __construct(Configuration $config, Transaction $transaction, PaymentResource $paymentResource) {
        parent::__construct(Configuration::pvChannel, 'pv', $config, $transaction, $paymentResource);
    }
    
    public function getVisibility() {
        return $this->config->isPvEnable() && 
               $this->config->isCurrencyForPv(
                    $this->transaction->getPayment()->getOrder()->getCurrency()
               );
    }
    
    public function getHiddenFields() {
        $data = parent::getHiddenFields();
        $data['id'] = $this->config->getPvId();
        return $data;
    }
}

?>
