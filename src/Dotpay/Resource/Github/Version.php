<?php

namespace Dotpay\Resource\Github;

use Dotpay\Validator\Url;
use Dotpay\Exception\BadParameter\UrlException;

class Version {
    private $number;
    private $url;
    private $zip;
    private $created;
    private $published;

    public function __construct($number, $zip) {
        $this->setNumber($number);
        $this->setZip($zip);
    }
    
    public function getNumber() {
        return $this->number;
    }

    public function getUrl() {
        return $this->url;
    }

    public function getZip() {
        return $this->zip;
    }

    public function getCreated() {
        return $this->created;
    }

    public function getPublished() {
        return $this->published;
    }

    public function setNumber($number) {
        $this->number = str_replace('v', '', $number);
        return $this;
    }

    public function setUrl($url) {
        if(!Url::validate($url))
            throw new UrlException($url);
        $this->url = $url;
        return $this;
    }

    public function setZip($zip) {
        if(!Url::validate($zip))
            throw new UrlException($zip);
        $this->zip = $zip;
        return $this;
    }

    public function setCreated(\DateTime $created) {
        $this->created = $created;
        return $this;
    }

    public function setPublished(\DateTime $published) {
        $this->published = $published;
        return $this;
    }


}