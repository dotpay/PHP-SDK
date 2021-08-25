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

namespace Dotpay\Processor;

use Dotpay\Action\UpdateCcInfo;
use Dotpay\Action\MakePaymentOrRefund;
use Dotpay\Model\Configuration;
use Dotpay\Model\Payment;
use Dotpay\Model\Notification;
use Dotpay\Model\Seller;
use Dotpay\Tool\IpDetector;
use Dotpay\Resource\Payment as PaymentResource;
use Dotpay\Resource\Seller as SellerResource;
use Dotpay\Exception\SellerNotRecognizedException;
use Dotpay\Exception\Processor\ConfirmationDataException;
use Dotpay\Exception\Processor\ConfirmationInfoException;

/**
 * Processor of confirmation activity.
 */
class Confirmation
{

    /**
     * @var Configuration Object of Dotpay configuration
     */
    private $config;

    /**
     * @var PaymentResource Object of payment resource
     */
    private $paymentApi;

    /**
     * @var SellerApi Object of seller resource
     */
    private $sellerApi;

    /**
     * @var Payment Object with payment data
     */
    private $payment;

    /**
     * @var Notification Object with notification data
     */
    private $notification;

    /**
     * @var UpdateCcInfo Action object which is executed during updateing a credit card data
     */
    private $updateCcAction;

    /**
     * @var MakePaymentOrRefund Action object which is executed during making a payment
     */
    private $makePaymentAction;

    /**
     * @var MakePaymentOrRefund Action object which is executed during making a refund
     */
    private $makeRefundAction;

    /**
     * Initialize the processor.
     *
     * @param Configuration   $config     Object of Dotpay configuration
     * @param PaymentResource $paymentApi Object of payment resource
     * @param SellerResource  $sellerApi  Object of seller resource
     */
    public function __construct(Configuration $config, PaymentResource $paymentApi, SellerResource $sellerApi)
    {
        $this->config = $config;
        $this->paymentApi = $paymentApi;
        $this->sellerApi = $sellerApi;
    }

    /**
     * Set an action which is executed during updateing a credit card data.
     *
     * @param UpdateCcInfo $updateCcAction Action object which is executed during updateing a credit card data
     *
     * @return Confirmation
     */
    public function setUpdateCcAction(UpdateCcInfo $updateCcAction)
    {
        $this->updateCcAction = $updateCcAction;

        return $this;
    }

    /**
     * Set an action which is executed during making a payment.
     *
     * @param MakePaymentOrRefund $makePaymentAction Action object which is executed during making a payment
     *
     * @return Confirmation
     */
    public function setMakePaymentAction(MakePaymentOrRefund $makePaymentAction)
    {
        $this->makePaymentAction = $makePaymentAction;

        return $this;
    }

    /**
     * Set an action which is executed during making a refund.
     *
     * @param MakePaymentOrRefund $makeRefundAction Action object which is executed during making a refund
     *
     * @return Confirmation
     */
    public function setMakeRefundAction(MakePaymentOrRefund $makeRefundAction)
    {
        $this->makeRefundAction = $makeRefundAction;

        return $this;
    }

    /**
     * Execute the processor for making all confirmation's activities.
     *
     * @param Payment      $payment      Payment data
     * @param Notification $notification Notification data
     *
     * @return bool
     *
     * @throws ConfirmationInfoException Thrown when info for customer service can be cought and displayed
     */
    public function execute(Payment $payment, Notification $notification)
    {
        $this->payment = $payment;
        $this->notification = $notification;
        $config = $this->config;

        $this->checkIp();
        $this->checkMethod();
        $this->checkCurrency();
        $this->checkSignature();

        $operation = $this->notification->getOperation();
        switch ($operation->getType()) {
            case $operation::TYPE_PAYMENT:
                return $this->makePayment();
            case $operation::TYPE_REFUND:
                return $this->makeRefund();
            default:
                return false;
        }
    }

    /**
     * Check if the IP address of a notification is correct.
     *
     * @return bool
     *
     * @throws ConfirmationDataException Thrown when IP address of a notification is incorrect
     */
    protected function checkIp()
    {
        $config = $this->config;
        if (
            !(IpDetector::detect($this->config) == $config::CALLBACK_IP || $_SERVER['REMOTE_ADDR'] == $config::CALLBACK_IP || IpDetector::detect($this->config) == $config::OFFICE_IP || $_SERVER['REMOTE_ADDR'] == $config::OFFICE_IP )
           ) 
            {
                throw new ConfirmationDataException('ERROR (REMOTE ADDRESS: '.IpDetector::detect($this->config).'/'.$_SERVER['REMOTE_ADDR'].')');
            }

        return true;
    }

    /**
     * Check if a HTTP method used during confirmation is correct.
     *
     * @return bool
     *
     * @throws ConfirmationDataException Thrown when sed HTTP method is different than POST
     */
    protected function checkMethod()
    {
        if (strtoupper($_SERVER['REQUEST_METHOD']) != 'POST') {
            throw new ConfirmationDataException('ERROR (METHOD <> POST)');
        }

        return true;
    }

    /**
     * Check if the notification data is correct through calculating a signature.
     *
     * @return bool
     *
     * @throws ConfirmationDataException Thrown if the given signature is different than calculated based on the notification data
     */
    protected function checkSignature()
    {
        if ($this->notification->calculateSignature($this->getSeller()->getPin()) != $this->notification->getSignature()) {
              throw new ConfirmationDataException('ERROR SIGNATURE - CHECK PIN');
          //  throw new ConfirmationDataException('ERROR SIGNATURE - CHECK PIN:'.$this->notification->calculateSignature($this->getSeller()->getPin(),'check')); // for debug ONLY!
        }

        return true;
    }

    /**
     * Check if the given currency is compatible with a currency of the order.
     *
     * @return bool
     *
     * @throws ConfirmationDataException Thrown when the given currency is different than original currency
     */
    protected function checkCurrency()
    {
        $receivedCurrency = $this->notification->getOperation()->getOriginalCurrency();
        $orderCurrency = $this->payment->getCurrency();
        if ($receivedCurrency != $orderCurrency) {
            throw new ConfirmationDataException('ERROR NO MATCH OR WRONG CURRENCY - '.$receivedCurrency.' <> '.$orderCurrency);
        }

        return true;
    }

    /**
     * Check if the given currency is compatible with a currency of the order.
     *
     * @return bool
     *
     * @throws ConfirmationDataException Thrown when the given amount is different than original amount
     */
    protected function checkPaymentAmount()
    {
        $receivedAmount = $this->notification->getOperation()->getOriginalAmount();
        $orderAmount = $this->payment->getAmount();
        if ($receivedAmount != $orderAmount) {
            throw new ConfirmationDataException('ERROR NO MATCH OR WRONG AMOUNT - '.$receivedAmount.' <> '.$orderAmount);
        }

        return true;
    }

    /**
     * Make a payment and execute all additional actions.
     *
     * @return bool
     */
    protected function makePayment()
    {
        $config = $this->config;
        $this->checkPaymentAmount();
        $operation = $this->notification->getOperation();
        if (
           $this->notification->getChannelId() == $config::OC_CHANNEL &&
           $this->updateCcAction !== null
          ) {
            $creditCard = null;
            if($operation->getStatus() == $operation::STATUS_COMPLETE)
            {
                if ($this->notification->getCreditCard() == null) {
                    $creditCard = $this->notification->getCreditCard();               
                    if($this->sellerApi->isAccountRight()) {
                        $operationFromApi = $this->sellerApi->getOperationByNumber($operation->getNumber());
                        $paymentMethod = $operationFromApi->getPaymentMethod();
                        if ($paymentMethod !== null && $paymentMethod->getDetailsType() == $paymentMethod::CREDIT_CARD) {
                            $creditCard = $paymentMethod->getDetails();
                        }
                    }
                }

                if ($creditCard !== null) {
                    $this->updateCcAction->setCreditCard($creditCard);
                    $this->updateCcAction->execute();
                }
            }
            elseif ($operation->getStatus() == $operation::STATUS_REJECTED)
            {
                $this->updateCcAction->setCreditCard($creditCard);
                $this->updateCcAction->execute();
            }

        }
        if ($this->makePaymentAction !== null) {
            $this->makePaymentAction->setOperation($operation);

            return $this->makePaymentAction->execute();
        } else {
            return true;
        }
    }

    /**
     * Make a refund and execute all additional actions.
     *
     * @return bool
     */
    protected function makeRefund()
    {
        if($this->makeRefundAction !== null)
        {
            $this->makeRefundAction->setOperation($this->notification->getOperation());
            return $this->makeRefundAction->execute();
        }

        return true;
    }

    /**
     * Return a Seller object with a data of seller which applies the given notivication.
     *
     * @return Seller
     *
     * @throws SellerNotRecognizedException Thrown when a seller is not recognized in configuration
     */
    protected function getSeller()
    {
        switch ($this->notification->getOperation()->getAccountId()) {
            case $this->config->getId():
                return new Seller($this->config->getId(), $this->config->getPin(), $this->config->getTestMode());
            case $this->config->getFccId():
                if (
                    $this->config->getFccVisible() &&
                    $this->config->isCurrencyForFcc(
                        $this->notification->getOperation()->getOriginalCurrency()
                    )
                  ) {
                    return new Seller($this->config->getFccId(), $this->config->getFccPin(), $this->config->getTestMode());
                } else {
                    throw new SellerNotRecognizedException($this->notification->getAccountId());
                }
            default:
                throw new SellerNotRecognizedException($this->notification->getAccountId());
        }
    }
}