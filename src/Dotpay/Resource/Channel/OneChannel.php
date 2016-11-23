<?php

namespace Dotpay\Resource\Channel;

class OneChannel {
    private $data;
    
    public function __construct(array $data) {
        $this->data = $data;
    }
    
    public function getId() {
        return $this->get('id');
    }
    
    public function getName() {
        return $this->get('name');
    }
    
    public function getLogo() {
        return $this->get('logo');
    }
    
    public function getGroup() {
        return $this->get('group');
    }
    
    public function getGroupName() {
        return $this->get('group_name');
    }
    
    public function getShortName() {
        return $this->get('short_name');
    }
    
    public function isDisabled() {
        return ($this->get('is_disable') !== "False");
    }
    
    public function isNotOnline() {
        return ($this->get('is_not_online') !== "False");
    }
    
    public function getFormNames() {
        return $this->get('form_names');
    }
    
    protected function get($name) {
        if(isset($this->data[$name]))
            return $this->data[$name];
        else
            return null;
    }
}