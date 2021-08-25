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

use Dotpay\Html\Form\Text;
use Dotpay\Html\Form\Label;
use Dotpay\Model\Configuration;
use Dotpay\Model\Transaction;
use Dotpay\Resource\Payment as PaymentResource;
use Dotpay\Resource\Seller as SellerResource;
use Dotpay\Validator\BlikCode;
use Dotpay\Exception\BadParameter\BlikCodeException;

/**
 * Class provides a special functionality for Blik payments.
 */
class Blik extends Channel
{
    const CODE = 'blik';

    /**
     * @var int Blik code which can be used for making a payment
     */
    private $blikCode = '';

    /**
     * @var string Description of the field with BLIK code
     */
    private $fieldDescription = '';

    /**
     * Initialize a Blik channel.
     *
     * @param Configuration   $config          Dotpay configuration object
     * @param Transaction     $transaction     Object with transaction details
     * @param PaymentResource $paymentResource Payment resource which can be used for Payment API
     * @param SellerResource  $sellerResource  Seller resource which can be used for Seller API
     */
    public function __construct(Configuration $config, Transaction $transaction, PaymentResource $paymentResource, SellerResource $sellerResource)
    {
        parent::__construct(Configuration::BLIK_CHANNEL, self::CODE, $config, $transaction, $paymentResource, $sellerResource);
    }

    /**
     * Check if the channel is visible.
     *
     * @return bool
     */
    public function isVisible()
    {
        return parent::isVisible() &&
               $this->config->getBlikVisible() &&
               ($this->transaction->getPayment()->getCurrency() === 'PLN');
    }

    /**
     * Return a Blik code which was set for a current payment.
     *
     * @return int
     */
    public function getBlikCode()
    {
        return $this->blikCode;
    }

    /**
     * Set a Blik code for a current payment.
     *
     * @param int $blikCode Blik code
     *
     * @return Blik
     *
     * @throws BlikCodeException Thrown if the Blik code is incorrect
     */
    public function setBlikCode($blikCode)
    {
        if (!BlikCode::validate($blikCode)) {
            throw new BlikCodeException($blikCode);
        }
        $this->blikCode = (int) $blikCode;

        return $this;
    }

    /**
     * Return an array of hidden fields for a form to redirecting to a Dotpay site with all needed information about a current payment.
     *
     * @return array
     */
    protected function prepareHiddenFields()
    {
        $data = parent::prepareHiddenFields();
        if (!$this->config->getTestMode()) {
            $data['blik_code'] = $this->blikCode;
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
        $field = new Text('blik_code');
        $field->setClass('dotpay_blik_code');
        if (!empty($this->fieldDescription)) {
            $field = new Label($field, $this->fieldDescription);
        }
        $data[] = $field;

        return $data;
    }

    /**
     * Set the description of the BLIK field.
     *
     * @param string $description Description of the BLIK field
     *
     * @return Blik
     */
    public function setFieldDescription($description)
    {
        $this->fieldDescription = (string) $description;

        return $this;
    }
}
