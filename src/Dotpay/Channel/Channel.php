<?php

namespace Dotpay\Channel;

use Dotpay\Resource\Payment as PaymentResource;
use Dotpay\Model\Payment as PaymentModel;
use Dotpay\Model\Transaction;
use Dotpay\Model\Configuration;
use Dotpay\Model\Notification;
use Dotpay\Exception\Resource\Channel\NotFoundException;

class Channel {
    protected $code;
    protected $reqistry = [];
    protected $config;
    protected $channelInfo;
    protected $agreements;
    protected $visibility;
    protected $available;
    protected $transaction;
    protected $resource;

    public function __construct($channelId, $code, Configuration $config, Transaction $transaction, PaymentResource $resource) {
        $this->code = $code;
        $this->config = $config;
        $this->transaction = $transaction;
        $this->visibility = true;
        $this->resource = $resource;
        $this->setChannelInfo($channelId);
    }
    
    public function set($name, $value) {
        $this->reqistry[$name] = $value;
    }
    
    public function get($name) {
        return $this->reqistry[$name];
    }
    
    public function getChannelId() {
        if($this->channelInfo !== null)
            return $this->channelInfo->getId();
        else return null;
    }
    
    public function getName() {
        if($this->channelInfo !== null)
            return $this->channelInfo->getName();
        else return null;
    }
    
    public function getLogo() {
        if($this->channelInfo !== null)
            return $this->channelInfo->getLogo();
        else return null;
    }
    
    public function getCode() {
        return $this->code;
    }
    
    public function setVisibility($visibility) {
        $this->visibility = (bool)$visibility;
    }
    
    public function isVisible() {
        return $this->visibility;
    }
    
    final public function isAvailable() {
        return $this->available;
    }
    
    public function isEnabled() {
        return $this->isVisible() && 
               $this->isAvailable() && 
               $this->config->isGatewayEnabled(
                    $this->transaction->getPayment()->getOrder()->getCurrency()
               );
    }
    
    public function getViewFields() {
        $data = array();
        return $data;
    }
    
    public function getHiddenFields() {
        $data = [];
        $data['id'] = $this->config->getId();
        $data['control'] = $this->transaction->getPayment()->getOrder()->getReference();
        $data['p_info'] = $this->config->getShopName();
        $data['amount'] = $this->transaction->getPayment()->getOrder()->getAmount();
        $data['currency'] = $this->transaction->getPayment()->getOrder()->getCurrency();
        $data['description'] = $this->transaction->getPayment()->getOrder()->getDescription();
        $data['lang'] = $this->transaction->getPayment()->getCustomer()->getLanguage();
        $data['URL'] = $this->transaction->getBackUrl();
        $data['URLC'] = $this->transaction->getConfirmUrl();
        $data['api_version'] = $this->config->getApi();
        $data['type'] = 4;
        $data['ch_lock'] = 1;
        $data['firstname'] = $this->transaction->getPayment()->getCustomer()->getFirstName();
        $data['lastname'] = $this->transaction->getPayment()->getCustomer()->getLastName();
        $data['email'] = $this->transaction->getPayment()->getCustomer()->getEmail();
        $data['phone'] = $this->transaction->getPayment()->getCustomer()->getPhone();
        $data['street'] = $this->transaction->getPayment()->getCustomer()->getStreet();
        $data['street_n1'] = $this->transaction->getPayment()->getCustomer()->getBuildingNumber();
        $data['city'] = $this->transaction->getPayment()->getCustomer()->getCity();
        $data['postcode'] = $this->transaction->getPayment()->getCustomer()->getPostCode();
        $data['country'] = $this->transaction->getPayment()->getCustomer()->getCountry();
        $data['bylaw'] = 1;
        $data['personal_data'] = 1;
        return $data;
    }
    
    public function getAgreements() {
        return $this->agreements;
    }
    
    public function getTransaction() {
        return $this->transaction;
    }
    
    public function beforeStartProcessingTransaction(PaymentModel $payment) {
        return true;
    }
    
    public function afterConfirmTransaction(Notification $notification, PaymentModel $payment) {
        return true;
    }
    
    protected function setChannelInfo($channelId) {
        try {
            $channelsData = $this->resource->getChannelList($this->transaction->getPayment());
            $this->channelInfo = $channelsData->getChannelInfo($channelId);
            $this->agreements = $channelsData->getAgreements($channelId);
            $this->available = true;
        } catch (NotFoundException $e) {
            $this->available = false;
        }
        
    }
}

?>
