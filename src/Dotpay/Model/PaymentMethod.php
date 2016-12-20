<?php

namespace Dotpay\Model;

use Dotpay\Validator\ChannelId;
use Dotpay\Exception\BadParameter\ChannelIdException;

class PaymentMethod {
    private $channelId;
    private $details;
    private $detailsType;
    
    const BANK_ACCOUNT = 1;
    const CREDIT_CARD = 2;
    
    public function __construct($channelId, $details = null, $detailsType = null) {
        $this->setChannelId($channelId);
        $this->setDetails($details);
        $this->setDetailsType($detailsType);
    }
    
    public function getChannelId() {
        return $this->channelId;
    }

    public function getDetails() {
        return $this->details;
    }
    
    public function getDetailsType() {
        return $this->detailsType;
    }

    public function setChannelId($channelId) {
        if(!ChannelId::validate($channelId))
            throw new ChannelIdException($channelId);
        $this->channelId = $channelId;
        return $this;
    }

    public function setDetails($details) {
        $this->details = $details;
        return $this;
    }
    
    public function setDetailsType($type) {
        $this->detailsType = $type;
        return $this;
    }
}