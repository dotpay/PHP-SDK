<?php

namespace Dotpay\Model\Payment;

class Method {
    private $channelId;
    private $details;
    
    public function __construct($channelId, $details) {
        $this->channelId = $channelId;
        $this->details = $details;
    }
    
    public function getChannelId() {
        return $this->channelId;
    }

    public function getDetails() {
        return $this->details;
    }

    public function setChannelId($channelId) {
        $this->channelId = $channelId;
        return $this;
    }

    public function setDetails($details) {
        $this->details = $details;
        return $this;
    }
}