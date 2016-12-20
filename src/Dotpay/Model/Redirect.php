<?php

namespace Dotpay\Model;

use Dotpay\Validator\Url;
use Dotpay\Exception\BadParameter\UrlException;
use Dotpay\Exception\BadParameter\MethodException;

class Redirect {
    const ALLOWED_METHODS = [
        'get', 'post', 'put', 'delete'
    ];
    
    private $url;
    private $data;
    private $method;
    private $encoding;
    
    public function __construct($url, array $data, $method = 'post', $encoding = 'utf-8') {
        $this->setUrl($url);
        $this->setData($data);
        $this->setMethod($method);
        $this->setEncoding($encoding);
    }
    
    public function getUrl() {
        return $this->url;
    }

    public function getData() {
        return $this->data;
    }

    public function getMethod() {
        return $this->method;
    }

    public function getEncoding() {
        return $this->encoding;
    }

    public function setUrl($url) {
        if(!Url::validate($url))
            throw new UrlException($url);
        $this->url = $url;
        return $this;
    }

    public function setData(array $data) {
        $this->data = $data;
        return $this;
    }

    public function setMethod($method) {
        $method = strtolower($method);
        if(array_search($method, self::ALLOWED_METHODS) === false)
            throw new MethodException($method);
        $this->method = $method;
        return $this;
    }

    public function setEncoding($encoding) {
        $this->encoding = $encoding;
        return $this;
    }
}