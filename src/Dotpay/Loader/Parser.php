<?php

namespace Dotpay\Loader;

use Dotpay\Loader\Xml\Object;
use Dotpay\Loader\Xml\Param;
use Dotpay\Exception\Loader\XmlNotFoundException;
use \SimpleXMLElement;

class Parser {
    private $xml;
    private $objects = [];
    
    public function __construct($fileName) {
        if(!file_exists($fileName))
            throw new XmlNotFoundException($fileName);
        $this->xml = new SimpleXMLElement(file_get_contents($fileName));
    }
    
    public function getObjects() {
        if(empty($this->objects))
            $this->parse();
        return $this->objects;
    }
    
    private function parse() {
        foreach($this->xml->object as $xmlObject) {
            $params = [];
            foreach ($xmlObject->param as $xmlParam)
                $params[] = new Param($xmlParam['class'], $xmlParam['name'], $xmlParam['value']);
            $this->objects[(string)$xmlObject['class']] = new Object($xmlObject['class'], $params, $xmlObject['alias']);
        }
    }
}