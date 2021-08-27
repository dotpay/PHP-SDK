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
use Dotpay\Resource\Channel\Info;
use Dotpay\Resource\Channel\Agreement;
use Dotpay\Validator\ChannelId;
use Dotpay\Exception\BadParameter\ChannelIdException;

/**
 * Class provides a special functionality for MasterPass payments.
 */
class Other extends Channel
{
    const CODE = 'other';

    /**
     * Initialize a MasterPass channel.
     *
     * @param Configuration   $config          Dotpay configuration object
     * @param Transaction     $transaction     Object with transaction details
     * @param PaymentResource $paymentResource Payment resource which can be used for Payment API
     * @param SellerResource  $sellerResource  Seller resource which can be used for Seller API
     */
    public function __construct(Configuration $config, Transaction $transaction, PaymentResource $paymentResource, SellerResource $sellerResource)
    {
        parent::__construct(null, self::CODE, $config, $transaction, $paymentResource, $sellerResource);
    }

    /**
     * Check if the channel is visible.
     *
     * @return bool
     */
    public function isVisible()
    {
        return parent::isVisible() && $this->config->getOtherChannelsVisible();
    }

    /**
     * Set a payment channel which is used.
     *
     * @param int $id Payment channel id
     *
     * @return Other
     */
    public function setChannelId($id)
    {
        $this->setChannelInfo($id);

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
        $channelId = $this->getChannelId();
        if (empty($channelId) || !$this->config->getOtherChannelsVisible()) {
            $data['type'] = '0';
            $data['ch_lock'] = '0';
        } else {
            $data['channel'] = (string) $channelId;
        }

        return $data;
    }

    /**
     * Retrieve informations about the channel from Dotpay server.
     *
     * @param int $channelId Code number of payment channel in Dotpay system
     *
     * @throws ChannelIdException Thrown if the given channel id isn't correct
     */
    protected function setChannelInfo($channelId = null)
    {
        if ($channelId !== null && !ChannelId::validate($channelId)) {
            throw new ChannelIdException($channelId);
        }
        try {
            $channelsData = $this->paymentResource->getChannelListForTransaction($this->transaction);
            $this->agreements = $this->prepareAgreements($channelsData);
            $this->available = true;
            if ($channelId !== null) {
                $this->channelInfo = $channelsData->getChannelInfo($channelId);
            } else {
                $this->channelInfo = null;
            }
            unset($channelsData);
        } catch (NotFoundException $e) {
            $this->available = false;
        }
    }

    /**
     * Prepare agreements list from the given information.
     *
     * @param Info $channelInfo Structure with information about channel
     *
     * @return array
     */
    protected function prepareAgreements(Info $channelInfo)
    {
        $agreements = [];
        foreach ($channelInfo->getForms() as $form) {
            if (isset($form['form_name']) && $form['form_name'] == 'agreement' && isset($form['fields'])) {
                foreach ($form['fields'] as $field) {
                    if ($field['required']) {
                        $agreements[] = new Agreement($field);
                    }
                }
            }
        }

        return $agreements;
    }
}
