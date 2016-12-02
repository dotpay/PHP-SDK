<?php

namespace Dotpay\Tool;

use Dotpay\Exception\ExtensionNotFoundException;

class Curl {
    private $curl;
    private $info;
    private $active = 0;
    
    public function __construct() {
		if (extension_loaded('curl') == false) {
			throw new ExtensionNotFoundException('curl');
		}
        $this->curl = curl_init();
        if($this->curl !== null)
            $this->active = 1;
    }
    
    public function __destruct() {
        if($this->active) {
            $this->close();
            $this->active = 0;
        }
    }

    public function addOption($option, $value) {
        curl_setopt($this->curl, $option, $value);
        return $this;
    }
    
    public function exec() {
        $response = curl_exec($this->curl);
        $this->info = curl_getinfo($this->curl);
        return $response;
    }
    
    public function error() {
        return curl_error($this->curl);
    }
    
    public function getInfo() {
        return $this->info;
    }
    
    public function close() {
        curl_close($this->curl);
        $this->active = 0;
    }
}

?>
