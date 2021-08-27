<?php
/**
 * Copyright (c) 2021 PayPro S.A. <tech@dotpay.pl>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @author    Dotpay Team <tech@dotpay.pl>
 * @copyright PayPro S.A.
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */

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
use Dotpay\Locale\Translator;
use Dotpay\Locale\Adapter\Csv;
use Dotpay\Resource\Channel\Agreement;
use Dotpay\Bootstrap;

/**
 * Class provides a special functionality for credit card payments, realized as an one Click method.
 */
class Oc extends Channel
{
    const CODE = 'oc';

    /**
     * @var CreditCard A credit card object which is assigned to this channel
     */
    private $card;

    /**
     * @var array An array of all available credit cards
     */
    private $cardList = [];

    /**
     * @var string An URL to a place in a shop where a customer can manage saved credit cards
     */
    private $manageCardsUrl;

    /**
     * @var string Description of saved cards option
     */
    private $savedCardsDescription = '';

    /**
     * @var string Description of register a new card option
     */
    private $registerCardDescription = '';

    /**
     * @var string Description of manage card URL
     */
    private $manageCardsDescription = '';

    /**
     * Initialize a credit card channel for the One Click method.
     *
     * @param Configuration   $config          Dotpay configuration object
     * @param Transaction     $transaction     Object with transaction details
     * @param PaymentResource $paymentResource Payment resource which can be used for Payment API
     * @param SellerResource  $sellerResource  Seller resource which can be used for Seller API
     */
    public function __construct(Configuration $config, Transaction $transaction, PaymentResource $paymentResource, SellerResource $sellerResource)
    {
        parent::__construct(Configuration::OC_CHANNEL, self::CODE, $config, $transaction, $paymentResource, $sellerResource);
    }

    /**
     * Check if the channel is visible.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return parent::isEnabled() && $this->config->isOcEnable();
    }

    /**
     * Return a credit card which is assigned.
     *
     * @return CreditCard
     */
    public function getCard()
    {
        return $this->card;
    }

    /**
     * Assign an credit card object to the channel.
     *
     * @param CreditCard $card An credit card object
     *
     * @return Oc
     */
    public function setCard(CreditCard $card)
    {
        $this->card = $card;
        if ($this->card->getUserId() == '' || $this->card->getUserId() == null) {
            $this->card->setUserId($this->transaction->getCustomer()->getId());
        }
        if ($this->card->getOrderId() == null) {
            $this->card->setOrderId($this->transaction->getPayment()->getId());
        }

        return $this;
    }

    /**
     * Add a new credit card to a list of all available CCs.
     *
     * @param CreditCard $card An credit card object
     *
     * @return Oc
     */
    public function addCard(CreditCard $card)
    {
        $this->cardList[] = $card;

        return $this;
    }

    /**
     * Return an array of all available credit cards.
     *
     * @return array
     */
    public function getCardList()
    {
        return $this->cardList;
    }

    /**
     * Return an URL to a place in a shop where a customer can manage saved credit cards.
     *
     * @return string
     */
    public function getManageCardsUrl()
    {
        return $this->manageCardsUrl;
    }

    /**
     * Set an URL to a place in a shop where a customer can manage saved credit cards.
     *
     * @param string $url The given URL address
     *
     * @return Oc
     */
    public function setManageCardsUrl($url)
    {
        $this->manageCardsUrl = (string) $url;

        return $this;
    }

    /**
     * Return array of hidden fields for a form to redirecting to a Dotpay site with all needed information about a current payment.
     *
     * @return array
     */
    protected function prepareHiddenFields()
    {
        $data = parent::prepareHiddenFields();
        $data['credit_card_customer_id'] = (string) $this->getCard()->getCustomerHash();
        if ($this->getCard()->getCardId() == null) {
            $data['credit_card_store'] = "1";
        } else {
            $data['credit_card_id'] = (string) $this->getCard()->getCardId();
        }

        return $data;
    }

    /**
     * Return an array of fields which can be displayed on a list of payment channels.
     * They can contain aditional fields with information which are needed before continue a payment process.
     *
     * @return array
     */
    public function getViewFields()
    {
        $data = parent::getViewFields();
        $numberOfcards = count($this->getCardList());
        if ($numberOfcards) {
            $data[] = new Radio('dotpay_oc_mode', 'select');
            $select = new Select('dotpay_card_list');
            foreach ($this->getCardList() as $card) {
                if ($card->isRegistered()) {
                    $select->addOption(new Option($card->getMask(), $card->getId()));
                }
            }
            if ($numberOfcards == 1) {
                $select->setSelected($this->cardList[0]->getId());
            }
            $data[] = $select;
        }
        $data[] = new Radio('dotpay_oc_mode', 'register');

        return $data;
    }

    /**
     * Return view fields enriched by an additional piece of HTML code.
     *
     * @return array
     */
    public function getViewFieldsHtml()
    {
        $data = $this->getViewFields();
        if (count($data) > 1) {
            return [
                $this->createSelectCardOption($data[0]),
                $this->createSelectCardList($data[1]),
                $this->createRegisterCardOption($data[2]),
            ];
        } elseif (count($data) == 1) {
            return [$this->createRegisterCardOption($data[0])];
        }
    }

    /**
     * Set a description of saved cards option.
     *
     * @param string $description Description of saved cards option
     *
     * @return Oc
     */
    public function setSavedCardsDescription($description)
    {
        $this->savedCardsDescription = (string) $description;

        return $this;
    }

    /**
     * Set a description of register of a new card.
     *
     * @param string $description Description of register of a new card
     *
     * @return Oc
     */
    public function setRegisterCardDescription($description)
    {
        $this->registerCardDescription = (string) $description;

        return $this;
    }

    /**
     * Set a description of manage cards URL.
     *
     * @param string $description Description of manage cards URL
     *
     * @return Oc
     */
    public function setManageCardsDescription($description)
    {
        $this->manageCardsDescription = (string) $description;

        return $this;
    }

    /**
     * Return special agreement only for One Click channel.
     *
     * @return \Dotpay\Resource\Channel\Agreement
     */
    public static function getSpecialAgreements()
    {
        $newAgreements = [];
        $translator = new Translator(new Csv(Bootstrap::getLocaleDir()));
        $description = $translator->__('I agree to repeated loading bill my credit card for the payment One-Click by way of purchase of goods or services offered by the store.');
        $newAgreements[] = new Agreement([
            'type' => 'check',
            'name' => 'oc-store-card',
            'label' => 'One Click Agreement',
            'required' => true,
            'default' => true,
            'description_text' => $description,
            'description_html' => $description,
        ]);

        return $newAgreements;
    }

    /**
     * Create a HTML package for "select card option".
     *
     * @param Element $element HTML element with "select card option"
     *
     * @return Label
     */
    protected function createSelectCardOption(Element $element)
    {
        $a = new A($this->getManageCardsUrl(), $this->manageCardsDescription);
        $a->setAttribute('target', '_blank');
        $checkLabel = new Label($element, '', $this->savedCardsDescription.' ('.$a.')');
        $checkLabel->setAttribute('class', $element->getName());

        return $checkLabel;
    }

    /**
     * Create a HTML package for "select card list".
     *
     * @param Element $element HTML element with credit card list
     *
     * @return Div
     */
    protected function createSelectCardList(Element $element)
    {
        $img = new Img('');
        $img->setClass('dotpay-card-logo');
        foreach ($this->getCardList() as $card) {
            if ($card->isRegistered()) {
                $img->setData('card-'.$card->getId(), $card->getBrand()->getImage());
            }
        }
        $div = new Div([$element, $img]);
        $div->setAttribute('class', $element->getName());

        return $div;
    }

    /**
     * Create a HTML package for "register card option".
     *
     * @param Element $element HTML element with "register card option"
     *
     * @return Label
     */
    protected function createRegisterCardOption(Element $element)
    {
        $regLabel = new Label($element, '', $this->registerCardDescription);
        $regLabel->setAttribute('class', $element->getName());

        return $regLabel;
    }
}
