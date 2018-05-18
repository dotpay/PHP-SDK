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

namespace Dotpay\Channel;

use Dotpay\Model\Configuration;
use Dotpay\Model\Transaction;
use Dotpay\Resource\Payment as PaymentResource;
use Dotpay\Resource\Seller as SellerResource;
use Dotpay\Html\Container\P;
use Dotpay\Html\Container\A;
use Dotpay\Html\Container\Div;
use Dotpay\Html\Container\Script;
use Dotpay\Html\PlainText;
use Dotpay\Resource\Channel\Info;
use Dotpay\Resource\Channel\Agreement;
use Dotpay\Validator\ChannelId;
use Dotpay\Exception\BadParameter\ChannelIdException;

/**
 * Class provides a special functionality for Dotpay standard payments.
 */
class Dotpay extends Channel
{
    const CODE = 'dotpay';

    /**
     * @var string Description of Dotpay widget
     */
    private $selectChannelTitle = '';

    /**
     * @var string Description of "change channel" button
     */
    private $changeChannel = '';

    /**
     * @var string Description of available channels option
     */
    private $availableChannelsTitle = '';
    /**
     * Initialize a Dotpay standard channel.
     *
     * @param Configuration   $config          Dotpay configuration object
     * @param Transaction     $transaction     Object with transaction details
     * @param PaymentResource $paymentResource Payment resource which can be used for Payment API
     * @param SellerResource  $sellerResource  Seller resource which can be used for Seller API
     */
    public function __construct(Configuration $config, Transaction $transaction, PaymentResource $paymentResource, SellerResource $sellerResource)
    {
        parent::__construct(null, self::CODE, $config, $transaction, $paymentResource, $sellerResource);
        $this->available = true;
    }

    /**
     * Check if the channel is visible.
     *
     * @return bool
     */
    public function isVisible()
    {
        return parent::isVisible() &&
               $this->config->isWidgetEnabled(
                    $this->transaction->getPayment()->getCurrency()
               );
    }

    /**
     * Set a payment channel which is used.
     *
     * @param int $id Payment channel id
     *
     * @return Dotpay
     */
    public function setChannelId($id)
    {
        $this->setChannelInfo($id);

        return $this;
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
        if ($this->config->getWidgetVisible() && $this->isVisible()) {
            $config = $this->config;

            $link = new A('#', $this->changeChannel.'&nbsp;&raquo;');
            $link->setClass('channel-selected-change');

            $div1 = new Div([
                new PlainText($this->selectChannelTitle.':&nbsp;&nbsp;'),
                $link,
            ]);
            $div1->setClass('selected-channel-message');
            $data[] = $div1;

            $div2 = new Div(new PlainText('<hr />'));
            $div2->setClass('selectedChannelContainer channels-wrapper');
            $data[] = $div2;

            $div3 = new Div(new PlainText($this->availableChannelsTitle.':'));
            $div3->setClass('collapsibleWidgetTitle');
            $data[] = $div3;

            $container = new P();
            $container->setClass($config::WIDGET_CLASS_CONTAINER);
            $data[] = $container;
        }

        return $data;
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
        if (empty($channelId) || !$this->config->getWidgetVisible()) {
            $data['type'] = 0;
            $data['ch_lock'] = 0;
        } else {
            $data['channel'] = $channelId;
        }

        return $data;
    }

    /**
     * Return a Script element with data which contains a configuration of Dotpay widget.
     *
     * @param array $disableChanels List of ids of channels which are used as separated payment channels
     *
     * @return Script
     */
    public function getScript(array $disableChanels = [])
    {
        $config = $this->config;
        $script = [
            'sellerAccountId' => $this->config->getId(),
            'amount' => $this->transaction->getPayment()->getAmount(),
            'currency' => $this->transaction->getPayment()->getCurrency(),
            'lang' => $this->transaction->getCustomer()->getLanguage(),
            'widgetFormContainerClass' => $config::WIDGET_CLASS_CONTAINER,
            'offlineChannel' => 'mark',
            'offlineChannelTooltip' => true,
            'disabledChannels' => $disableChanels,
            'host' => $this->config->getPaymentUrl().'payment_api/channels/',
        ];

        return new Script(new PlainText('var dotpayWidgetConfig = '.json_encode($script).';'));
    }

    /**
     * Set a description of Dotpay widget.
     *
     * @param string $selectChannelTitle Description of Dotpay widget
     *
     * @return Dotpay
     */
    public function setSelectChannelTitle($selectChannelTitle)
    {
        $this->selectChannelTitle = (string) $selectChannelTitle;

        return $this;
    }

    /**
     * Set a description of "change channel" button.
     *
     * @param string $changeChannel Description of "change channel" button
     *
     * @return Dotpay
     */
    public function setChangeChannel($changeChannel)
    {
        $this->changeChannel = (string) $changeChannel;

        return $this;
    }

    /**
     * Set a description of available channels option.
     *
     * @param string $availableChannelsTitle Description of available channels option
     *
     * @return Dotpay
     */
    public function setAvailableChannelsTitle($availableChannelsTitle)
    {
        $this->availableChannelsTitle = (string) $availableChannelsTitle;

        return $this;
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
