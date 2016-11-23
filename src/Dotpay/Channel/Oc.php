<?php

namespace Dotpay\Channel;

use Dotpay\Model\Configuration;
use Dotpay\Model\Transaction;
use Dotpay\Resource\Payment as PaymentResource;
use Dotpay\Resource\Seller as SellerResource;
use Dotpay\Model\CreditCard;
use Dotpay\Html\Form\Label;
use Dotpay\Html\Form\Radio;
use Dotpay\Html\Form\Select;
use Dotpay\Html\Form\Option;
use Dotpay\Html\Container\A;
use Dotpay\Html\Container\Div;
use Dotpay\Html\Img;
use Dotpay\Html\Element;

class Oc extends Channel {
    private $card;
    private $cardList = [];
    private $sellerResource;
    private $manageCardsUrl;
    
    public function __construct(Configuration $config, Transaction $transaction, PaymentResource $paymentResource, SellerResource $sellerResource) {
        parent::__construct(Configuration::ocChannel, 'oc', $config, $transaction, $paymentResource);
        $this->sellerResource = $sellerResource;
    }
    
    public function getVisibility() {
        return $this->config->isOcEnable();
    }
    
    public function getCard() {
        return $this->card;
    }
    
    public function setCard(CreditCard $card) {
        $this->card = $card;
        return $this;
    }
    
    public function addCard(CreditCard $card) {
        $this->cardList[] = $card;
        return $this;
    }
    
    public function getCardList() {
        return $this->cardList;
    }
    
    public function getManageCardsUrl() {
        return $this->manageCardsUrl;
    }
    
    public function setManageCardsUrl($url) {
        $this->manageCardsUrl = $url;
        return $this;
    }
    
    public function getHiddenFields() {
        $data = parent::getHiddenFields();
        $data['credit_card_customer_id'] = $this->getCard()->getUserId();
        if($this->getCard()->getCardId() == null) {
            $data['credit_card_store'] = 1;
        } else {
            $data['credit_card_id'] = $this->getCard()->getCardId();
        }
        return $data;
    }
    
    public function getViewFields() {
        $data = parent::getViewFields();
        $numberOfcards = count($this->getCardList());
        if($numberOfcards) {
            $data[] = new Radio('dotpay_select_card', 1);
            $select = new Select('dotpay_card_list');
            foreach($this->getCardList() as $card)
                if($card->isRegistered())
                    $select->addOption(new Option($card->getMask(), $card->getId()));
            if($numberOfcards == 1)
                $select->setSelected($this->cardList[0]->getId());
            $data[] = $select;
        }
        $data[] = new Radio('dotpay_register_card', 1);
        return $data;
    }
    
    public function getViewFieldsHtml() {
        $data = $this->getViewFields();
        return [
            $this->createSelectCardOption($data[0]),
            $this->createSelectCardList($data[1]),
            $this->createRegisterCardOption($data[2])
        ];
    }
    
    protected function createSelectCardOption(Element $element) {
        $a = new A($this->getManageCardsUrl(), 'Manage your saved credit cards');
        $a->setAttribute('target', '_blank');
        $checkLabel = new Label($element, '', 'Select from saved cards'.' ('.$a.')');
        $checkLabel->setAttribute('class', $element->getName());
        return $checkLabel;
    }
    
    protected function createSelectCardList(Element $element) {
        $img = new Img('');
        $img->setClass('dotpay-card-logo');
        foreach($this->getCardList() as $card) {
            if($card->isRegistered())
                $img->setData('card-'.$card->getId(), $card->getBrand()->getLogo());
        }
        $div = new Div([$element, $img]);
        $div->setAttribute('class', $element->getName());
        return $div;
    }
    
    protected function createRegisterCardOption(Element $element) {
        $regLabel = new Label($element, '', 'Register new card');
        $regLabel->setAttribute('class', $element->getName());
        return $regLabel;
    }
}

?>
