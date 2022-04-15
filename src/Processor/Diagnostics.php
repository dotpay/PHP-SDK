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

use Dotpay\Exception\Processor\ConfirmationDataException;
use Dotpay\Exception\SellerNotRecognizedException;
use Dotpay\Model\Configuration;
use Dotpay\Model\Seller;
use Dotpay\Resource\Payment as PaymentResource;
use Dotpay\Resource\Seller as SellerResource;
use Dotpay\Tool\IpDetector;

/**
 * Processor of diagnostics activity.
 */
class Diagnostics
{
    /**
     * @var string Container of a collected message
     */
    private $outputMessage;

    /**
     * @var string Additional info about plugin version
     */
    private $pluginInfo;

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
     * Initialize the processor.
     *
     * @param Configuration   $config     Object of Dotpay configuration
     * @param PaymentResource $paymentApi Object of payment resource
     * @param SellerResource  $sellerApi  Object of seller resource
     */
    public function __construct(Configuration $config, PaymentResource $paymentApi, SellerResource $sellerApi, $pluginInfo = null)
    {
        $this->config = $config;
        $this->paymentApi = $paymentApi;
        $this->sellerApi = $sellerApi;
        $this->outputMessage = '';
        $this->pluginInfo = $pluginInfo;

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
		$productMetadata = $objectManager->get('Magento\Framework\App\ProductMetadataInterface'); 
        $request = $objectManager->get('Magento\Framework\App\Request\Http');  
		$this->MagentoVersion = $productMetadata->getVersion();
		$this->get_dp_debug = $request->getParam('dp_debug');

        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $this->MagentoUrl =  $this->getHost($storeManager->getStore()->getBaseUrl());


    }


    /**
     * Parsing domain from a URL
     */
    protected function getHost($url) { 
        $parseUrl = parse_url(trim($url)); 
        if(isset($parseUrl['host']))
        {
            $host = $parseUrl['host'];
        }
        else
        {
             $path = explode('/', $parseUrl['path']);
             $host = $path[0];
        }
        return trim($host); 
     }



    /**
     * Execute the processor for making all confirmation's activities.
     *
     * @return bool
     *
     */
    public function execute()
    {
        $config = $this->config;
        $dp_debug_allow = false;

        if( (int)$config->getNonProxyMode() == 1) {
            $clientIp = $_SERVER['REMOTE_ADDR'];
            $proxy_desc = 'FALSE';
        }else{
            $clientIp = IpDetector::detect($this->config);
            $proxy_desc = 'TRUE';
        }
        

        $string_to_hash = 'h:'.$this->MagentoUrl.',id:'.$config->getId().',d:'.date('YmdHi').',p:'.$config->getPin();
        $dp_debug_hash = hash('sha256', $string_to_hash);


        if( isset($this->get_dp_debug) && trim($this->get_dp_debug) == $dp_debug_hash ) {
            $dp_debug_allow = true;
        }else{
            $dp_debug_allow = false;
        }

       if ( (strtoupper($_SERVER['REQUEST_METHOD']) == 'GET') && ($clientIp == $config::OFFICE_IP || $dp_debug_allow == true) ) 
        {
            $this->completeInformations();
            die($this->outputMessage);

        //only FOR DOTPAY DEBUG SYSTEM
        } else if(isset($this->get_dp_debug) && $this->get_dp_debug == "time" && (strtoupper($_SERVER['REQUEST_METHOD']) == 'GET'))
        {
                $this->addOutputMessage('IP: '.IpDetector::detect($this->config).'/'.$_SERVER['REMOTE_ADDR'].', PROXY: '.$proxy_desc.', METHOD: '.$_SERVER['REQUEST_METHOD'].", TIME: ".date('YmdHi'), true);
                die($this->outputMessage);
        } 
        /*
                else if ((strtoupper($_SERVER['REQUEST_METHOD']) == 'GET') && ($clientIp != $config::OFFICE_IP) && (!isset($this->get_dp_debug) || $this->get_dp_debug != "time") ) 
                {
                    $this->addOutputMessage('IP: '.IpDetector::detect($this->config).'/'.$_SERVER['REMOTE_ADDR'].', PROXY: '.$proxy_desc.', METHOD: '.$_SERVER['REQUEST_METHOD']);
                    die($this->outputMessage);

                } 
        */
        else {
            return false;
        }
    }

    protected function DotpayModuleInfo()
    {
        return $this->pluginInfo;
    }

    /**
     * Collect informations about shop which can be displayed for diagnostic.
     */
    protected function completeInformations()
    {
        $config = $this->config;

        $this->addOutputMessage('--- Platform Information ---')
		->addOutputMessage('Magento Version: '.$this->MagentoVersion)
        ->addOutputMessage('PHP Version: '.  phpversion())
        ->addOutputMessage('Server datetime: '. date('YmdHi'));
        
        $this->addOutputMessage('<br><br>--- Dotpay Diagnostic Information ---')
            ->addOutputMessage('Sdk Version: '.$config::SDK_VERSION);
        if($this->pluginInfo)
        {
            $this->addOutputMessage("Plugin: ".$this->pluginInfo);
        }
        $this->addOutputMessage('Enabled: '.(int) $config->getEnable(), true)
            ->addOutputMessage('--- Dotpay PLN ---')
            ->addOutputMessage('&#127380; Id: '.$config->getId())
            ->addOutputMessage('Correct Id: '.(int) $this->paymentApi->checkSeller($config->getId()))
            ->addOutputMessage('Correct Pin: '.(int) $this->sellerApi->checkPin())
            ->addOutputMessage('API Version: '.$config->getApi())
            ->addOutputMessage('Test Mode: '.(int) $config->getTestMode())
            ->addOutputMessage('Hostname: '.$this->MagentoUrl)
            ->addOutputMessage('Not uses Proxy Mode: '.(int) $config->getNonProxyMode())
            ->addOutputMessage('Refunds: '.(int) $config->getRefundsEnable())
            ->addOutputMessage('Widget: '.(int) $config->getWidgetVisible())
            ->addOutputMessage('Widget currencies: '.$config->getWidgetCurrencies())
            ->addOutputMessage('Control field with NO additional information (default): '.$config->getControlDefault())
            ->addOutputMessage('Instructions: '.(int) $config->getInstructionVisible(), true)
            ->addOutputMessage('Store Name from: '.$config->getStoreName())
            ->addOutputMessage('Store Email from: '.$config->getStoreEmail())
            ->addOutputMessage('--- Separate Channels ---')
            ->addOutputMessage('One Click: '.(int) $config->getOcVisible())
            ->addOutputMessage('Credit Card: '.(int) $config->getCcVisible())
            ->addOutputMessage('MasterPass: '.(int) $config->getMpVisible())
            ->addOutputMessage('Blik: '.(int) $config->getBlikVisible())
            ->addOutputMessage('Other channels: '.implode(", ", $config->getOtherChannels()), true)
            ->addOutputMessage('--- Dotpay FCC ---')
            ->addOutputMessage('FCC Mode: '.(int) $config->getFccVisible())
            ->addOutputMessage('FCC Id: '.$config->getFccId())
            ->addOutputMessage('FCC Correct Id: '.(int) $this->paymentApi->checkSeller($config->getFccId()))
            ->addOutputMessage('FCC Correct Pin: '.(int) $this->sellerApi->checkFccPin())
            ->addOutputMessage('FCC Currencies: '.$config->getFccCurrencies(), true)
            ->addOutputMessage('--- REMOTE_ADDRESS ---')
            ->addOutputMessage('$_SERVER[\'REMOTE_ADDR\'] : '.$_SERVER['REMOTE_ADDR'])
            ->addOutputMessage('--- Dotpay API ---')
            ->addOutputMessage('Username and password are not empty: '.(($config->isGoodApiData()) ? '&lt;not empty&gt;' : '&lt;empty&gt;'))
            ->addOutputMessage('Login: '.$config->getUsername());
        try {
            $isAccountRight = $this->sellerApi->isAccountRight();
        } catch (\Exception $ex) {
            $isAccountRight = false;
        }
        $this->addOutputMessage('Correct data: '.$isAccountRight, true);
    }

    /**
     * Add a new message to te collector.
     *
     * @param string $message      Message to add
     * @param bool   $endOfSection Flag if the given message is last in a section
     *
     * @return Diagnostics
     */
    protected function addOutputMessage($message, $endOfSection = false)
    {
        $this->outputMessage .= $message.'<br />';
        if ($endOfSection) {
            $this->outputMessage .= '<br />';
        }

        return $this;
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

        if( (int)$config->getNonProxyMode() == 1) {
            $clientIp = $_SERVER['REMOTE_ADDR'];
            $proxy_desc = 'FALSE';
        }else{
            $clientIp = IpDetector::detect($this->config);
            $proxy_desc = 'TRUE';
        }

        if ( 
            !( IpDetector::isAllowedIp($clientIp, $config::DOTPAY_CALLBACK_IP_WHITE_LIST) )
           ) 
        {
            throw new ConfirmationDataException('ERROR (REMOTE ADDRESS: '.$clientIp.'/'.$_SERVER['REMOTE_ADDR'].', PROXY:'.$proxy_desc.')');
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
                    throw new SellerNotRecognizedException($this->notification->getOperation()->getAccountId());
                }
            default:
                throw new SellerNotRecognizedException($this->notification->getOperation()->getAccountId());
        }
    }
}