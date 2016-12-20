<?php

namespace Dotpay\Resource\RegisterOrder;

use Dotpay\Validator\Url;
use Dotpay\Model\Redirect;
use Dotpay\Model\Operation;
use Dotpay\Model\Instruction;
use Dotpay\Exception\BadParameter\UrlException;

class Result {
    private $statusUrl;
    private $redirect;
    private $operation;
    private $instruction;
    
    public function __construct($statusUrl, Operation $operation) {
        $this->setStatusUrl($statusUrl);
        $this->setOperation($operation);
    }
    
    public function getStatusUrl() {
        return $this->statusUrl;
    }

    public function getRedirect() {
        return $this->redirect;
    }

    public function getOperation() {
        return $this->operation;
    }

    public function getInstruction() {
        return $this->instruction;
    }

    public function setStatusUrl($statusUrl) {
        if(!Url::validate($statusUrl))
            throw new UrlException($statusUrl);
        $this->statusUrl = $statusUrl;
        return $this;
    }

    public function setRedirect(Redirect $redirect) {
        $this->redirect = $redirect;
        return $this;
    }

    public function setOperation(Operation $operation) {
        $this->operation = $operation;
        return $this;
    }

    public function setInstruction(Instruction $instruction) {
        $this->instruction = $instruction;
        return $this;
    }


}