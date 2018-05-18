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

namespace Dotpay\Resource\Channel;

use Dotpay\Validator\Id;
use Dotpay\Exception\BadParameter\IdException;
use Dotpay\Validator\Amount;
use Dotpay\Exception\BadParameter\AmountException;
use Dotpay\Validator\Currency;
use Dotpay\Exception\BadParameter\CurrencyException;
use Dotpay\Validator\Language;
use Dotpay\Exception\BadParameter\LanguageException;
use Dotpay\Exception\BadParameter\RequestFormatException;
use Dotpay\Model\Transaction;
use Dotpay\Model\Configuration;

/**
 * Class which contains data used for creating a payment request.
 */
class Request
{
    /**
     * @var array Possible formats of expected response
     */
    public static $AVAILABLE_FORMATS = ['json', 'xml'];

    /**
     * @var int Seller id
     */
    private $sellerId;

    /**
     * @var float Amount of the request
     */
    private $amount;

    /**
     * @var string Currency code of the request
     */
    private $currency;

    /**
     * @var string Language used by the customer
     */
    private $language;

    /**
     * @var bool A flag if test mode is used or not
     */
    private $testMode;

    /**
     * @var string Format of expected response
     */
    private $format = 'json';

    /**
     * Initialize the request.
     *
     * @param int    $sellerId Seller id
     * @param bool   $testMode Flag if test mode is activated
     * @param float  $amount   Amount of the request
     * @param string $currency Currency code of the request
     * @param string $language Language used by the customer
     */
    private function __construct($sellerId, $testMode, $amount = 300, $currency = 'PLN', $language = 'pl')
    {
        $this->setSellerId($sellerId);
        $this->setTestMode($testMode);
        if ($amount) {
            $this->setAmount($amount);
        }
        if ($currency) {
            $this->setCurrency($currency);
        }
        if ($language) {
            $this->setLanguage($language);
        }
    }

    /**
     * Creates an object of Request based on data given in transaction object.
     *
     * @param Transaction $transaction Data of transaction
     *
     * @return Request
     */
    public static function getFromTransaction(Transaction $transaction)
    {
        return new static(
            $transaction->getPayment()->getSeller()->getId(),
            $transaction->getPayment()->getSeller()->isTestMode(),
            $transaction->getPayment()->getAmount(),
            $transaction->getPayment()->getCurrency(),
            $transaction->getCustomer()->getLanguage()
        );
    }

    /**
     * Create request object based on povided data.
     *
     * @param int    $sellerId Seller id
     * @param bool   $testMode Flag if test mode is activated
     * @param float  $amount   Amount of the request
     * @param string $currency Currency code of the request
     * @param string $language Language used by the customer
     *
     * @return Request
     */
    public static function getFromData($sellerId, $testMode, $amount, $currency, $language)
    {
        return new static(
            $sellerId,
            $testMode,
            $amount,
            $currency,
            $language
        );
    }

    /**
     * Create request object based on seller account data.
     *
     * @param int  $sellerId Seller id
     * @param bool $testMode Flag if test mode is activated
     *
     * @return Request
     */
    public static function getFromSellerId($sellerId, $testMode)
    {
        return new static($sellerId, $testMode);
    }

    /**
     * Return seller id.
     *
     * @return int
     */
    public function getSellerId()
    {
        return $this->sellerId;
    }

    /**
     * Return amount of the request.
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Return currency code of the request.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Return language used by the customer.
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Return a flag if test mode is used or not.
     *
     * @return bool
     */
    public function isTestMode()
    {
        return $this->testMode;
    }

    /**
     * Return format of expected response.
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Return query string for the payment request.
     *
     * @return string
     */
    public function getQueryString()
    {
        return 'id='.$this->getSellerId().
               '&amount='.$this->getAmount().
               '&currency='.$this->getCurrency().
               '&lang='.$this->getLanguage().
               '&format='.$this->getFormat();
    }

    /**
     * Return full url of the request.
     *
     * @return string
     */
    public function getUrl()
    {
        $config = new Configuration('');
        $config->setTestMode($this->isTestMode());

        return $config->getPaymentUrl().'payment_api/channels/?'.$this->getQueryString();
    }

    /**
     * Return an identifier of the request.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->getSellerId().
               $this->getAmount().
               $this->getCurrency().
               $this->getLanguage();
    }

    /**
     * Set a seller id.
     *
     * @param int $sellerId Seller id
     *
     * @return Seller
     *
     * @throws IdException Thrown when the given seller id is incorrect
     */
    public function setSellerId($sellerId)
    {
        if (!Id::validate($sellerId)) {
            throw new IdException($sellerId);
        }
        $this->sellerId = (int) $sellerId;

        return $this;
    }

    /**
     * Set an amount of the request.
     *
     * @param float $amount An amount of the request
     *
     * @return Order
     *
     * @throws AmountException Thrown when the given amount is incorrect
     */
    public function setAmount($amount)
    {
        if (!Amount::validate($amount)) {
            throw new AmountException($amount);
        }
        $this->amount = floatval(str_replace(' ','',$amount));

        return $this;
    }

    /**
     * Set a currency code of the request.
     *
     * @param string $currency A currency code of the request
     *
     * @return Order
     *
     * @throws CurrencyException Thrown when the given currency is incorrect
     */
    public function setCurrency($currency)
    {
        $correctCurrency = strtoupper($currency);
        if (!Currency::validate($correctCurrency)) {
            throw new CurrencyException($correctCurrency);
        }
        $this->currency = (string) $correctCurrency;

        return $this;
    }

    /**
     * Set a language used by the customer.
     *
     * @param string $language Language used by the customer
     *
     * @return Request
     *
     * @throws LanguageException Thrown when the given language is incorrect
     */
    public function setLanguage($language)
    {
        if (!Language::validate($language)) {
            throw new LanguageException($language);
        }
        $this->language = (string) $language;

        return $this;
    }

    /**
     * Set a flag if test mode is used or not.
     *
     * @param bool $testMode Flag if test mode is used or not
     *
     * @return Request
     */
    public function setTestMode($testMode)
    {
        $this->testMode = (bool) $testMode;

        return $this;
    }

    /**
     * Set a format of expected response.
     *
     * @param string $format Format of expected response
     *
     * @return Request
     *
     * @throws RequestFormatException Thrown when the given format is incorrect
     */
    public function setFormat($format)
    {
        if (!in_array($format, self::$AVAILABLE_FORMATS)) {
            throw new RequestFormatException($format);
        }
        $this->format = (string) $format;

        return $this;
    }
}
