<?php

namespace Dotpay\Model;

class Payment {
    private $seller;
    private $customer;
    private $order;
    private $description;

    public function __construct(Seller $seller, Customer $customer, Order $order, $description) {
        $this->setSeller($seller);
        $this->setCustomer($customer);
        $this->setOrder($order);
        $this->setDescription($description);
    }
    
    public function getSeller() {
        return $this->seller;
    }

    public function getCustomer() {
        return $this->customer;
    }

    public function getOrder() {
        return $this->order;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setSeller(Seller $seller) {
        $this->seller = $seller;
        return $this;
    }

    public function setCustomer(Customer $customer) {
        $this->customer = $customer;
        return $this;
    }

    public function setOrder(Order $order) {
        $this->order = $order;
        return $this;
    }

    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }
    
    public function getIdentifier() {
        return $this->getSeller()->getId().$this->getOrder()->getAmount().$this->getOrder()->getCurrency().$this->getCustomer()->getLanguage();
    }
}