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

namespace Dotpay\Model;

use Dotpay\Validator\Url;
use Dotpay\Exception\BadParameter\UrlException;

/**
 * Informations about a transaction during which is realized the payment.
 */
class Transaction
{
    /**
     * @var Customer Customer model for the payment
     */
    private $customer;

    /**
     * @var Payment Payment which is realized by the transaction
     */
    private $payment;

    /**
     * @var CustomerAdditionalData CustomerAdditionalData model for the payment
     */
    private $customerAdditionalData;

    /**
     * @var array Subpayments using in multimerchant functionality
     */
    private $subPayments = [];

    /**
     * @var string Url where Dotpay server should redirect a customer
     */
    private $backUrl = '';

    /**
     * @var string Url where dotpay server should send a notification with status of payment
     */
    private $confirmUrl = '';

    /**
     * Initialize the transaction model.
     *
     * @param Customer $customer Customer model for the transaction
     * @param Payment  $payment  Payment which is realized by the transaction
     */
    public function __construct(Customer $customer, Payment $payment)
    {
        $this->setCustomer($customer)
             ->setPayment($payment);
    }

    /**
     * Return a customer who realize the transaction.
     *
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Return customer additional data.
     *
     * @return CustomerAdditionalData
     */
    public function getCustomerAdditionalData()
    {
        return $this->customerAdditionalData;
    }

    /**
     * Return a payment which is realized by the transaction.
     *
     * @return Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Return a list of subpayments.
     *
     * @return array
     */
    public function getSubPayments()
    {
        return $this->subPayments;
    }

    /**
     * Return an url where Dotpay server should redirect a customer.
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->backUrl;
    }

    /**
     * Return an url where dotpay server should send a notification with status of payment.
     *
     * @return string
     */
    public function getConfirmUrl()
    {
        return $this->confirmUrl;
    }

    /**
     * Set a customer who realize the transaction.
     *
     * @param Customer $customer A customer who realize the transaction
     *
     * @return Transaction
     */
    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Set a customer additional data field.
     *
     * @param CustomerAdditionalData $customerAdditionalData Customer additional data
     *
     * @return Transaction
     */
    public function setCustomerAdditionalData(CustomerAdditionalData $customerAdditionalData)
    {
        $this->customerAdditionalData = $customerAdditionalData;

        return $this;
    }

    /**
     * Set a payment which is realized by the transaction.
     *
     * @param Payment $payment A payment which is realized by the transaction
     *
     * @return Transaction
     */
    public function setPayment(Payment $payment)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Set an url where Dotpay server should redirect a customer.
     *
     * @param string $backUrl Url where Dotpay server should redirect a customer
     *
     * @return Transaction
     *
     * @throws UrlException Thrown when the given url is incorrect
     */
    public function setBackUrl($backUrl)
    {
        if (!Url::validate($backUrl)) {
            throw new UrlException($backUrl);
        }
        $this->backUrl = (string) $backUrl;

        return $this;
    }

    /**
     * Set an url where dotpay server should send a notification with status of payment.
     *
     * @param string $confirmUrl Url where dotpay server should send a notification with status of payment
     *
     * @return Transaction
     *
     * @throws UrlException Thrown when the given url is incorrect
     */
    public function setConfirmUrl($confirmUrl)
    {
        if (!Url::validate($confirmUrl)) {
            throw new UrlException($confirmUrl);
        }
        $this->confirmUrl = (string) $confirmUrl;

        return $this;
    }

    /**
     * Add a new subpayment to the transaction.
     *
     * @param Payment $subPayment The subpayment to add
     *
     * @return Transaction
     */
    public function addSubPayment(Payment $subPayment)
    {
        $this->subPayments[] = $subPayment;

        return $this;
    }

    /**
     * Return an identifier of the transaction, which depends on seller id, order amount, order currency and customer language.
     *
     * @return string
     */
    public function getdentifier()
    {
        return $this->payment->getIdentifier().$this->customer->getLanguage();
    }
}
