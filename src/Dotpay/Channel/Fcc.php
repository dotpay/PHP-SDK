<?php

namespace Dotpay\Channel;

use Dotpay\Model\Configuration;
use Dotpay\Model\Transaction;
use Dotpay\Resource\Payment as PaymentResource;

class Fcc extends Channel {
    public function __construct(Configuration $config, Transaction $transaction, PaymentResource $paymentResource) {
        parent::__construct(Configuration::fccChannel, 'fcc', $config, $transaction, $paymentResource);
    }
    
    public function getVisibility() {
        return $this->config->isFccEnable() && 
               $this->config->isCurrencyForFcc(
                    $this->transaction->getPayment()->getOrder()->getCurrency()
               );
    }
    
    public function getHiddenFields() {
        $data = parent::getHiddenFields();
        $data['id'] = $this->config->getFccId();
        return $data;
    }
}

?>
