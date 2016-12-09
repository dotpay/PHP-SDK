<?php

namespace Dotpay\Model;

use Dotpay\Validator\Amount;
use Dotpay\Exception\BadParameter\AmountException;

class Transfer
{
    private $control;
    private $amount;
    private $description;
    private $recipient;

    public function __construct($amount, $control, BankAccount $recipient)
    {
        $this->setAmount($amount);
        $this->setControl($control);
        $this->setRecipient($recipient);
    }

    public function getControl()
    {
        return $this->control;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getRecipient()
    {
        return $this->recipient;
    }

    public function setControl($control)
    {
        $this->control = $control;

        return $this;
    }

    public function setAmount($amount)
    {
        if (!Amount::validate($amount)) {
            throw new AmountException($amount);
        }
        $this->amount = $amount;

        return $this;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function setRecipient(BankAccount $recipient)
    {
        $this->recipient = $recipient;

        return $this;
    }
}
