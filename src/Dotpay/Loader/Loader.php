<?php

namespace Dotpay\Loader;

use \ReflectionClass;
use Dotpay\Loader\Xml\Param;
use Dotpay\Loader\Xml\Object;
use Dotpay\Exception\Loader\ObjectNotFoundException;
use Dotpay\Exception\Loader\ParamNotFoundException;
use Dotpay\Exception\Loader\EmptyObjectNameException;

class Loader {
    private static $instance = null;
    private $objects = [];
    private $aliases = [];
    
    public static function load(Parser $current = null, Parser $default = null) {
        if(empty(self::$instance))
            self::$instance = new Loader($current, $default);
        else if($current !== null || $default !== null)
            self::$instance->initialize($current, $default);
        return self::$instance;
    }
    
    private function __construct(Parser $current = null, Parser $default = null) {
        $this->initialize($current, $default);
    }
    
    private function initialize(Parser $current = null, Parser $default = null) {
        if($default !== null) {
            $this->updateObjects($default->getObjects());
        }
        if($current !== null) {
            $this->updateObjects($current->getObjects());
        }
    }
    
    public function get($name, array $params = []) {
        $xmlObject = $this->findObject($name);
        $arguments = [];
        if(empty($params)) {
            foreach($xmlObject->getParams() as $param) {
                if(!empty($param->getClassName()))
                    $arguments[] = $this->get($param->getClassName());
                else
                    $arguments[] = $param->getStoredValue();
            }
        } else {
            $arguments = $params;
        }
        return $this->getObjectInstance($xmlObject, $arguments);
    }
    
    public function set($className, $object, $alias = null) {
        $newObject = new Object($className, [], $alias);
        $newObject->setStoredInstance([], $object);
        $this->objects[$className] = $newObject;
        if($alias !== null)
            $this->aliases[$alias] = $newObject;
        return $newObject;
    }
    
    public function object($className, array $params = [], $alias = null) {
        $normalizedParams = [];
        foreach($params as $key => $value) {
            $paramName = is_string($key)?$key:null;
            $objParam = new Param(null, $paramName, null);
            $objParam->setStoredValue($value);
            $normalizedParams[] = $objParam;
        }
        $newObject = new Object($className, $normalizedParams, $alias);
        $this->objects[$className] = $newObject;
        if($alias !== null)
            $this->aliases[$alias] = $newObject;
        return $newObject;
    }
    
    public function parameter($name, $value) {
        if(!strpos($name, ':'))
            throw new ParamNotFoundException($name);
        list($className, $paramName) = explode(':', $name);
        $object = $this->findObject($className);
        return $object->setParamVal($paramName, $value);
    }
    
    private function findObject($name) {
        if($name === null)
            throw new EmptyObjectNameException();
        if(isset($this->aliases[$name])) {
            return $this->aliases[$name];
        } else if(isset($this->objects[$name])) {
            return $this->objects[$name];
        } else throw new ObjectNotFoundException($name);
    }
    
    private function getObjectInstance(Object $object, array $params = []) {
        if(empty($object->getStoredInstance($params))) {
            $reflection = new ReflectionClass($object->getClass());
            $instance = $reflection->newInstanceArgs($params);
            $object->setStoredInstance($params, $instance);
        }
        return $object->getStoredInstance($params);
    }
    
    private function updateObjects(array $objects) {
        foreach($objects as $object) {
            //removing alias where object is overwritten
            try {
                $oldObject = $this->findObject($object->getClass());
                if($this->findObject($oldObject->getAlias())) {
                    unset($this->aliases[$oldObject->getAlias()]);
                }
            } catch (\InvalidArgumentException $ex) {}
            //removing object where alias is overwritten
            try {
                $oldObject = $this->findObject($object->getAlias());
                if($this->findObject($oldObject->getClass())) {
                    unset($this->objects[$oldObject->getClass()]);
                }
            } catch (\InvalidArgumentException $ex) {}
            $this->objects[$object->getClass()] = $object;
            if(!empty($object->getAlias()))
                $this->aliases[$object->getAlias()] = $object;
        }
    }
}
