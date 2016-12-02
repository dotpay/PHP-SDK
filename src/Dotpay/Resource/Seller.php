<?php

namespace Dotpay\Resource;

use Dotpay\Model\Payment as PaymentModel;
use Dotpay\Model\Configuration;
use Dotpay\Tool\Curl;
use Dotpay\Exception\Resource\ApiException;
use Dotpay\Exception\BadReturn\TypeNotCompatibleException;

class Seller extends Resource {
    public function __construct(Configuration $config, Curl $curl) {
        parent::__construct($config, $curl);
        $this->curl->addOption(CURLOPT_USERPWD, $this->config->getUsername().':'.$this->config->getPassword());
    }

    public function isAccountRight() {
        try {
            $this->getDataFromApi('payments/');
        } catch (ApiException $e) {
            return false;
        }
        return true;
    }
    
    public function checkPin() {
        return $this->checkIdAndPin($this->config->getId(), $this->config->getPin());
    }
    
    public function checkPvPin() {
        return $this->checkIdAndPin($this->config->getPvId(), $this->config->getPvPin());
    }
    
    public function getPaymentByNumber($number) {
        
    }
    
    public function getPaymentById($id) {
        
    }
    
    public function makeRefund(PaymentModel $payment, $amount, $description) {
        
    }
    
    private function checkIdAndPin($id, $pin) {
        $response = $this->getDataFromApi('account/'.$id);
        if(isset($response['config']) &&
           isset($response['config']['pin']) &&
           $response['config']['pin'] == $pin) {
            return true;
        }
        else {
            return false;
        }
    }
    
    private function getDataFromApi($targetUrl) {
        $content = $this->getContent($this->getApiUrl($targetUrl));
        if(!is_array($content))
            throw new TypeNotCompatibleException(gettype($content));
        if(isset($content['error_code'])) {
            $exception = new ApiException($content['detail']);
            throw $exception->setApiCode($content['error_code']);
        }
        return $content;
    }
    
    private function getApiUrl($end) {
        return $this->config->getPaymentUrl().'api/'.$end;
    }
}