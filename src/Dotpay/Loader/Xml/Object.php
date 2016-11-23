<?php

namespace Dotpay\Loader\Xml;

use Dotpay\Exception\Loader\EmptyObjectNameException;

class Object {
    private $className;
    private $parameters = [];
    private $namedParameters = [];
    private $alias;
    private $storedInstance = [];
    
    public function __construct($className, array $parameters = [], $alias = null) {
        if(empty($className))
            throw new EmptyObjectNameException();
        $this->className = (string)$className;
        foreach ($parameters as $param)
            if($param instanceof Param) {
                $this->parameters[] = $param;
                if(!empty($param->getName()))
                    $this->namedParameters[$param->getName()] = $param;
            }
        $this->alias = (string)$alias;
    }
    
    public function getClass() {
        return $this->className;
    }
    
    public function getParams() {
        return $this->parameters;
    }
    
    public function getParamVal($name) {
        foreach($this->namedParameters as $key => $value)
            if($name === $key)
                return $value->getStoredValue();
        return null;
    }
    
    public function setParamVal($name, $value) {
        foreach($this->namedParameters as $key => $oldValue)
            if($name === $key) {
                $this->namedParameters[$name]->setStoredValue($value);
                break;
            }
        return $this;
    }
    
    public function getAlias() {
        return $this->alias;
    }
    
    public function getStoredInstance($params) {
        $paramId = sha1(serialize($params));
        return isset($this->storedInstance[$paramId])?$this->storedInstance[$paramId]:null;
    }
    
    public function setStoredInstance($params, $instance) {
        $paramId = sha1(serialize($params));
        $this->storedInstance[$paramId] = $instance;
        return $this;
    }
    
    public function getXml() {
        $element = '<object';
        if(!empty($this->getClass()))
            $element .= ' class=\''.$this->getClass().'\'';
        if(!empty($this->getAlias()))
            $element .= ' alias=\''.$this->getAlias().'\'';
        $element .= '>';
        foreach ($this->getParams() as $param)
            $element .= $param->getXml();
        $element .= '</object>';
        return $element;
    }
}