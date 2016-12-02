<?php

namespace Dotpay\Model;

use Dotpay\Validator\ChannelId;
use Dotpay\Exception\BadParameter\ChannelIdException;

class PaymentMethod {
    private $channelId;
    private $details;
    
    public function __construct($channelId, $details) {
        $this->setChannelId($channelId);
        $this->setDetails($details);
    }
    
    public function getChannelId() {
        return $this->channelId;
    }

    public function getDetails() {
        return $this->details;
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
}