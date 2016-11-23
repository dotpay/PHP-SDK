<?php

namespace Dotpay\Resource;

use Dotpay\Model\Payment as ModelPayment;
use Dotpay\Resource\Channel\Info;
use Dotpay\Exception\Resource\ApiException;
use Dotpay\Exception\BadReturn\TypeNotCompatibleException;

class Payment extends Resource {
    private $buffer;
    
    public function getChannelList(ModelPayment $payment) {
        $id = $payment->getIdentifier();
        if(!isset($this->buffer[$id])) {
            $content = $this->getContent($this->getUrl($payment));
            if(!is_array($content))
                throw new TypeNotCompatibleException(gettype($content));
            if(isset($content['error_code'])) {
                $exception = new ApiException($content['detail']);
                throw $exception->setApiCode($content['error_code']);
            }
            $this->buffer[$id] = new Info($content['channels'], $content['forms']);
        }
        return $this->buffer[$id];
    }
    
    private function getUrl(ModelPayment $payment) {
        $lang = $payment->getCustomer()->getLanguage();
        if(!$lang)
            $lang = 'fr';
        return $this->config->getPaymentUrl().'payment_api/channels/'.
               '?id='.$payment->getSeller()->getId().
               '&amount='.$payment->getOrder()->getAmount().
               '&currency='.$payment->getOrder()->getCurrency().
               '&lang='.$lang.
               '&format=json';
    }
}