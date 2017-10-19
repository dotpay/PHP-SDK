<?php
/**
 * Copyright (c) 2017 Dotpay S.A. <techdotpay.pl>.
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
 * @copyright Dotpay S.A
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */

namespace Dotpay\Model;

use Dotpay\Provider\PaymentProviderInterface;
use Dotpay\Validator\Amount;
use Dotpay\Exception\BadParameter\AmountException;
use Dotpay\Validator\Currency;
use Dotpay\Exception\BadParameter\CurrencyException;

/**
 * Informations about a payment.
 */
class Payment
{
    /**
     * @var int An id of the payment
     */
    private $id;

    /**
     * @var float An amount of the payment
     */
    private $amount;

    /**
     * @var string A currency code of the payment
     */
    private $currency;

    /**
     * @var string A description of the payment
     */
    private $description;

    /**
     * @var Seller|null A Seller model for the payment
     */
    private $seller = null;

    /**
     * Create Payment object from the given data.
     *
     * @param PaymentProviderInterface $provider Data provider
     *
     * @return Payment
     */
    public static function createFromData(PaymentProviderInterface $provider)
    {
        $payment = new static(
            $provider->getSeller(),
            $provider->getAmount(),
            $provider->getCurrency(),
            $provider->getDescription(),
            $provider->getId()
        );

        return $payment;
    }

    /**
     * Initialize the payment model.
     *
     * @param Seller $seller      A Seller model for the payment
     * @param float  $amount      An amount of the payment
     * @param string $currency    A currency code of the payment
     * @param string $description A description of the payment
     * @param string $id          An id of the payment
     */
    public function __construct(Seller $seller, $amount, $currency, $description, $id = '')
    {
        $this->setSeller($seller)
             ->setAmount($amount)
             ->setCurrency($currency)
             ->setDescription($description)
             ->setId($id);
    }

    /**
     * Return an id of the order.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return an amount of the order.
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Return a currency code of the order.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Return a description of the payment.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Return a Seller model for the payment.
     *
     * @return Seller|null
     */
    public function getSeller()
    {
        return $this->seller;
    }

    /**
     * Return an identifier of the payment, which depends on seller id, order amount, order currency.
     *
     * @return string
     */
    public function getIdentifier()
    {
        $sellerId = ($this->getSeller()) ? $this->getSeller()->getId() : '';

        return $sellerId.$this->getAmount().$this->getCurrency();
    }

    /**
     * Return an amount of surcharge which is calculated for the order.
     *
     * @param Configuration $config Configuration object
     *
     * @return float
     */
    public function getSurcharge(Configuration $config)
    {
        if (!$config->getSurcharge()) {
            return 0.0;
        }
        $exPercentage = $this->getAmount() * $config->getSurchargePercent() / 100;
        $exAmount = $config->getSurchargeAmount();

        return max($exPercentage, $exAmount);
    }

    /**
     * Set an id of the order.
     *
     * @param int $id An id of the order
     *
     * @return Order
     */
    public function setId($id)
    {
        $this->id = (int) $id;

        return $this;
    }

    /**
     * Set an amount of the order.
     *
     * @param float $amount An amount of the order
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
     * Set a currency code of the order.
     *
     * @param string $currency A currency code of the order
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
     * Set a description of the payment.
     *
     * @param string $description A description of the payment
     *
     * @return Payment
     */
    public function setDescription($description)
    {
        $this->description = (string) $description;

        return $this;
    }

    /**
     * Set a Seller model for the payment.
     *
     * @param Seller $seller A Seller model for the payment
     *
     * @return Payment
     */
    public function setSeller(Seller $seller)
    {
        $this->seller = $seller;

        return $this;
    }
}
