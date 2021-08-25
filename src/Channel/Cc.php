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

/**
 * Class provides a special functionality for credit card payments.
 */
class Cc extends Channel
{
    const CODE = 'cc';
    /**
     * Initialize a credit card channel.
     *
     * @param Configuration   $config          Dotpay configuration object
     * @param Transaction     $transaction     Object with transaction details
     * @param PaymentResource $paymentResource Payment resource which can be used for Payment API
     * @param SellerResource  $sellerResource  Seller resource which can be used for Seller API
     */
    public function __construct(Configuration $config, Transaction $transaction, PaymentResource $paymentResource, SellerResource $sellerResource)
    {
        parent::__construct(Configuration::OC_CHANNEL, self::CODE, $config, $transaction, $paymentResource, $sellerResource);
        if (!$this->available && $this->isVisible()) {
            $this->setChannelInfo(Configuration::CC_CHANNEL);
        }
    }

    /**
     * Check if the channel is visible.
     *
     * @return bool
     */
    public function isVisible()
    {
        return parent::isVisible() &&
                $this->config->getCcVisible() &&
                !($this->config->isFccEnable() &&
                $this->config->isCurrencyForFcc(
                    $this->transaction->getPayment()->getCurrency()
                ));
    }
}
