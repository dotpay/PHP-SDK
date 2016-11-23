<?php

namespace Dotpay\Model;

use Dotpay\Channel\Channel;
use Dotpay\Channel\Dotpay;
use Dotpay\Html\Container\Script;
use Dotpay\Html\PlainText;

use \ArrayAccess;
use \Countable;
use \Iterator;

class ChannelList implements ArrayAccess, Countable, Iterator {
    private $pointer;
    private $channels = [];
    private $config;
    
    public function __construct() {
        $this->rewind();
    }
    
    public function offsetExists($offset) {
        return isset($this->channels[$offset]);
    }
    
    public function offsetGet($offset) {
        return $this->channels[$offset];
    }
    
    public function offsetSet($offset, $value) {
        $this->channels[$offset] = $value;
    }
    
    public function offsetUnset($offset) {
        unset($this->channels[$offset]);
    }
    
    public function count() {
        return count($this->channels);
    }
    
    public function current() {
        return $this->channels[$this->key()];
    }
    
    public function key() {
        return $this->pointer;
    }
    
    public function next() {
        do {
            ++$this->pointer;
        } while($this->valid() && !$this->current()->isEnabled(1));
    }
    
    public function rewind() {
        $this->pointer = -1;
        $this->next();
    }
    
    public function valid() {
        return isset($this->channels[$this->key()]);
    }
    
    public function addChannel(Channel $channel) {
        $this->channels[] = $channel;
    }
    
    public function getChannelIds() {
        $ids = [];
        foreach($this as $channel) {
            if(!empty($channel->getChannelId()))
                $ids[] = $channel->getChannelId();
        }
        return $ids;
    }
    
    public function getWidgetScript() {
        foreach ($this as $channel)
            if($channel instanceof Dotpay)
                return $channel->getScript($this->getChannelIds());
        return new Script(new PlainText());
    }
}

