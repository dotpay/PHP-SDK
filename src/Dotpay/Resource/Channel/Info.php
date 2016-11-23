<?php

namespace Dotpay\Resource\Channel;

use Dotpay\Exception\Resource\Channel\NotFoundException;

class Info {
    private $channels;
    private $forms;
    public function __construct(array $channels, array $forms) {
        $this->channels = $channels;
        $this->forms = $forms;
    }
    
    public function getChannels() {
        return $this->channels;
    }
    
    public function getForms() {
        return $this->forms;
    }
    
    public function getChannelInfo($channelId) {
        foreach ($this->getChannels() as $channel) {
            if(isset($channel['id']) && $channel['id'] == $channelId) {
                return new OneChannel($channel);
            }
        }
        throw new NotFoundException($channelId);
    }
    
    public function getAgreements($channelId) {
        $channelInfo = $this->getChannelInfo($channelId);
        $fields = $channelInfo->getFormNames();
        $agreements = [];
        if(array_search('agreement', $fields) !== false) {
            foreach ($this->getForms() as $form) {
                if(isset($form['form_name']) && $form['form_name'] == 'agreement' && isset($form['fields']))
                    foreach($form['fields'] as $field)
                        if($field['required'])
                            $agreements[] = new Agreement($field);
            }
        }
        return $agreements;
    }
}