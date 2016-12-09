<?php

namespace Dotpay\Channel;

use Dotpay\Html\Form\Text;
use Dotpay\Model\Configuration;
use Dotpay\Model\Transaction;
use Dotpay\Resource\Payment as PaymentResource;
use Dotpay\Validator\BlikCode;
use Dotpay\Exception\BadParameter\BlikCodeException;

class Blik extends Channel {
    private $blikCode;
    
    public function __construct(Configuration $config, Transaction $transaction, PaymentResource $paymentResource) {
        parent::__construct(Configuration::blikChannel, 'blik', $config, $transaction, $paymentResource);
    }
    
    public function getVisibility() {
        return $this->config->getBlikVisible() && ($this->transaction->getPayment()->getOrder()->getCurrency() === 'PLN');
    }

    public function getBlikCode() {
        return $this->blikCode;
    }
    
    public function setBlikCode($blikCode) {
        if(!BlikCode::validate($blikCode))
            throw new BlikCodeException($blikCode);
        $this->blikCode = $blikCode;
        return $this;
    }
    
    public function getHiddenFields() {
        $data = parent::getHiddenFields();
        $data['blik_code'] = $this->blikCode;
        return $data;
    }
    
    public function getViewFields() {
        $data = parent::getViewFields();
        $field = new Text('blik_code');
        $field->setClass('dotpay_blik_code');
        $data[] = $field;
        return $data;
    }
}

?>
