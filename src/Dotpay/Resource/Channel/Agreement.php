<?php

namespace Dotpay\Resource\Channel;

use Dotpay\Resource\Payment as ResourcePayment;
use Dotpay\Model\Payment as ModelPayment;

class Agreement {
    private $data;
    
    public function __construct(array $data) {
        $this->data = $data;
    }
    
    public function getType() {
        return $this->get('type');
    }
    
    public function getName() {
        return $this->get('name');
    }
    
    public function getLabel() {
        return $this->get('label');
    }
    
    public function getRequired() {
        return (bool)$this->get('required');
    }
    
    public function getDefault() {
        return (bool)$this->get('default');
    }
    
    public function getDescription() {
        return $this->get('description_text');
    }
    
    public function getDescriptionHtml() {
        return $this->get('description_html');
    }
    
    protected function get($name) {
        if(isset($this->data[$name]))
            return $this->data[$name];
        else
            return null;
    }
}