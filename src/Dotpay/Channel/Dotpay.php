<?php

namespace Dotpay\Channel;

use Dotpay\Model\Configuration;
use Dotpay\Model\Transaction;
use Dotpay\Resource\Payment as PaymentResource;
use Dotpay\Html\Container\P;
use Dotpay\Html\Container\Script;
use Dotpay\Html\PlainText;

class Dotpay extends Channel {
    public function __construct(Configuration $config, Transaction $transaction, PaymentResource $resource) {
        parent::__construct(null, 'dotpay', $config, $transaction, $resource);
        $this->available = true;
    }
    
    public function isVisible() {
        return $this->config->isWidgetEnabled($this->transaction->getPayment()->getOrder()->getCurrency());
    }
    
    public function setChannelId($id) {
        $this->setChannelInfo($id);
    }
    
    public function getViewFields() {
        $data = parent::getViewFields();
        if($this->config->getWidgetVisible() && $this->isVisible()) {
            $config = $this->config;
            $container = new P();
            $container->setClass($config::widgetClassContainer);
            $data[] = $container;
        }
        return $data;
    }

    public function getHiddenFields() {
        $data = parent::getHiddenFields();
        if(empty($this->getChannelId()) || !$this->config->getWidgetVisible()) {
            $data['type'] = 0;
            $data['ch_lock'] = 0;
        } else {
            $data['channel'] = $this->getChannelId();
        }
        return $data;
    }
    
    public function getScript(array $disableChanels = []) {
        $config = $this->config;
        $script = [
            'sellerAccountId' => $this->config->getId(),
            'amount' => $this->transaction->getPayment()->getOrder()->getAmount(),
            'currency' => $this->transaction->getPayment()->getOrder()->getCurrency(),
            'lang' => $this->transaction->getPayment()->getCustomer()->getLanguage(),
            'widgetFormContainerClass' => $config::widgetClassContainer,
            'offlineChannel' => 'mark',
            'offlineChannelTooltip' => true,
            'disabledChannels' => $disableChanels,
            'host' => $this->config->getPaymentUrl()
        ];
        return new Script(new PlainText('var dotpayWidgetConfig = '.json_encode($script).';'));
    }
}

?>
