<?php
/**
 * Copyright (c) 2018 Dotpay sp. z o.o. <tech@dotpay.pl>.
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
 * @copyright Dotpay sp. z o.o.
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */

namespace Dotpay\Model;

use DateTime;
use Dotpay\Provider\PaymentLinkProviderInterface;
use Dotpay\Validator\Url;
use Dotpay\Validator\Amount;
use Dotpay\Exception\BadParameter\UrlException;
use Dotpay\Exception\BadParameter\AmountException;
use Dotpay\Exception\BadParameter\CurrencyException;

/**
 * Informations about an operation.
 */
class PaymentLink
{
    /**
     * @var string An Url where are located details about the operation
     */
    private $url = '';

    /**
     * @var string An Urlc where are located details about the operation
     */
    private $urlc = '';

    /**
     * @var string An Urlc where are located details about the operation
     */
    private $href = '';

    /**
     * @var string An Urlc where are located details about the operation
     */
    private $paymentUrl = '';

    /**
     * @var string An Urlc where are located details about the operation
     */
    private $token = '';

    /**
     * @var string An Urlc where are located details about the operation
     */
    private $language = '';

    /**
     * @var string An Urlc where are located details about the operation
     */
    private $ignoreLastPaymentChannel = '';

    /**
     * @var string An identifier of a type of the operation
     */
    private $type = '';

    /**
     * @var float|null A transaction amount
     */
    private $amount = null;

    /**
     * @var string A code of a transaction currency
     */
    private $currency = '';

    /**
     * @var mixed A value which was given during making a payment
     */
    private $control = null;

    /**
     * @var string A description of the operation
     */
    private $description = '';

    /**
     * @var Payer|null A Payer object which contains information about payer
     */
    private $payer = null;



    /**
     * Create the model based on data provided from shop.
     *
     * @param PaymentLinkProviderInterface $provider Provider which contains data from shop application
     *
     * @return Operation
     */
    public static function createFromData(PaymentLinkProviderInterface $provider)
    {
        $paymentLink = new static();
        $paymentLink
            ->setAmount($provider->getAmount())
            ->setType($provider->getType())
            ->setCurrency($provider->getCurrency())
            ->setDescription($provider->getDescription())
            ->setControl($provider->getControl())
            ->setPayer(Customer::createFromData($provider->getPayer()));


        return $paymentLink;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return PaymentLink
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrlc()
    {
        return $this->urlc;
    }

    /**
     * @param string $urlc
     * @return PaymentLink
     */
    public function setUrlc($urlc)
    {
        $this->urlc = $urlc;
        return $this;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * @param string $href
     * @return PaymentLink
     */
    public function setHref($href)
    {
        $this->href = $href;
        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentUrl()
    {
        return $this->paymentUrl;
    }

    /**
     * @param string $paymentUrl
     * @return PaymentLink
     */
    public function setPaymentUrl($paymentUrl)
    {
        $this->paymentUrl = $paymentUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return PaymentLink
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     * @return PaymentLink
     */
    public function setLanguage($language)
    {
        $this->language = $language;
        return $this;
    }

    /**
     * @return string
     */
    public function getIgnoreLastPaymentChannel()
    {
        return $this->ignoreLastPaymentChannel;
    }

    /**
     * @param string $ignoreLastPaymentChannel
     * @return PaymentLink
     */
    public function setIgnoreLastPaymentChannel($ignoreLastPaymentChannel)
    {
        $this->ignoreLastPaymentChannel = $ignoreLastPaymentChannel;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return PaymentLink
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float|null $amount
     * @return PaymentLink
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return PaymentLink
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getControl()
    {
        return $this->control;
    }

    /**
     * @param mixed $control
     * @return PaymentLink
     */
    public function setControl($control)
    {
        $this->control = $control;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return PaymentLink
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return Customer|null
     */
    public function getPayer()
    {
        return $this->payer;
    }

    /**
     * @param Payer|null $payer
     * @return PaymentLink
     */
    public function setPayer($payer)
    {
        $this->payer = $payer;
        return $this;
    }

}
