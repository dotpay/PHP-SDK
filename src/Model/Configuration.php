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

use DateTime;
use Dotpay\Exception\BadParameter\FccIdException;
use Dotpay\Exception\BadParameter\FccPinException;
use Dotpay\Provider\ConfigurationProviderInterface;
use Dotpay\Validator\Id;
use Dotpay\Validator\Pin;
use Dotpay\Validator\Username;
use Dotpay\Exception\SellerNotFoundException;
use Dotpay\Exception\BadParameter\IdException;
use Dotpay\Exception\BadParameter\PinException;
use Dotpay\Exception\BadParameter\UsernameException;
use Dotpay\Exception\BadParameter\PasswordException;
use Dotpay\Exception\BadParameter\ApiVersionException;

/**
 * Storage of basic configuration.
 */
class Configuration
{
    /**
     * Version of the SDK.
     */
    const SDK_VERSION = '1.0.18';

    const DOTPAY_SSL_URL = 'https://ssl.dotpay.pl';

    /**
     * Url of Dotpay payment production server.
     */
    const PAYMENT_URL_PROD = 'https://ssl.dotpay.pl/t2/';

    /**
     * Url of Dotpay payment test server.
     */
    const PAYMENT_URL_DEV = 'https://ssl.dotpay.pl/test_payment/';

    /**
     * Url of Dotpay seller production server.
     */
    const SELLER_URL_PROD = 'https://ssl.dotpay.pl/s2/login/';

    /**
     * Url of Dotpay seller test server.
     */
    const SELLER_URL_DEV = 'https://ssl.dotpay.pl/test_seller/';

    /**
     * Address IP of Dotpay confirmation server.
     */
    const CALLBACK_IP = '195.150.9.37';

    /**
     * Address IP od Dotpay office.
     */
    const OFFICE_IP = '77.79.195.34';

    /**
     * Address IP of the localhost.
     */
    const LOCAL_IP = '127.0.0.1';

    /**
     * Id of One Click card channel.
     */
    const OC_CHANNEL = 248;

    /**
     * Id of card channel, used for foreign currencies.
     */
    const FCC_CHANNEL = 248;

    /**
     * Id of standard chard channel.
     */
    const CC_CHANNEL = 246;

    /**
     * Id of MasterPass channel.
     */
    const MP_CHANNEL = 71;

    /**
     * Id of BLIK channel.
     */
    const BLIK_CHANNEL = 73;

    /**
     * Id of PayPal channel.
     */
    const PAYPAL_CHANNEL = 212;

    /**
     * Class name of the HTML container which contains aDotpay widget on a payment site.
     */
    const WIDGET_CLASS_CONTAINER = 'dotpay-widget-container';

    /**
     * List of all supported currencies.
     */
    public static $CURRENCIES = [
        'EUR',
        'USD',
        'GBP',
        'JPY',
        'CZK',
        'SEK',
        'UAH',
        'RON',
        'PLN',
        'NOK',
        'BGN',
        'CHF',
        'HRK',
        'HUF',
        'RUB'
    ];
    
    /**
     * @var bool Flag which inform if other channels are enabled in a shop
     */
    private $otherChannelsVisible = false;

    /**
     * List of payment methods pulled out of the main channel
     */
    private $otherChannels = [];

    /**
     * @var string Id of plugin where is used SDK
     */
    private $pluginId = '';

    /**
     * @var bool Flag which inform if Dotpay payment is enabled in a shop
     */
    private $enable = false;

    /**
     * @var int|null Seller id
     */
    private $id = null;

    /**
     * @var string Seller pin
     */
    private $pin = '';

    /**
     * @var string Username of Dotpay seller dashboard
     */
    private $username = '';

    /**
     * @var string Password of Dotpay seller dashboard
     */
    private $password = '';

    /**
     * @var bool Flag if test mode is activated
     */
    private $testMode = false;

    /**
     * @var bool Flag if One Click card channel is visible
     */
    private $ocVisible = false;

    /**
     * @var bool Flag if card channel for foreign currencies is visible
     */
    private $fccVisible = false;

    /**
     * @var int|null Seller id for an account which is signed to support payment by card using foreign currencies
     */
    private $fccId = null;

    /**
     * @var string Seller pin for an account which is signed to support payment by card using foreign currencies
     */
    private $fccPin = '';

    /**
     * @var string Codes of currencies for which is allowed the FCC card channel.
     *             Every code is separated by "," character
     */
    private $fccCurrencies = '';

    /**
     * @var bool Flag if normal card channel is visible
     */
    private $ccVisible = false;

    /**
     * @var bool Flag if MasterPass channel is visible
     */
    private $mpVisible = false;

    /**
     * @var bool Flag if BLIK channel is visible
     */
    private $blikVisible = false;

    /**
     * @var bool Flag if Paypal channel is visible
     */
    private $paypalVisible = false;

    /**
     * @var bool Flag if Dotpay widget is visible on a payment page
     */
    private $widgetVisible = true;

    /**
     * @var string Codes of currencies for which is disallowed the Dotpay main channel.
     *             Every code is separated by "," character
     */
    private $widgetCurrencies = '';

    /**
     * @var bool Flag if payment instruction of cash or transfer channels should be visible on a shop site
     */
    private $instructionVisible = true;

    /**
     * @var bool Flag if refunds requesting is enabled from a shop system
     */
    private $refundsEnable = false;

    /**
     * @var bool Flag if renew payments are enabled for customers
     */
    private $renew = false;

    /**
     * @var int Number of days, how long after creating an order should be available renew option
     */
    private $renewDays = 0;

    /**
     * @var bool Flag if special surcharge is enabled
     */
    private $surcharge = false;

    /**
     * @var float Amount which will be added as a surcharge
     */
    private $surchargeAmount = 0.0;

    /**
     * @var float Percent of value of order which will be added as a surcharge
     */
    private $surchargePercent = 0.0;

    /**
     * @var string Name of shop which is sent to Dotpay server
     */
    private $shopName = '';

    private $storeName = '';

    /**
     * @var string Email of shop which is sent to Dotpay server
     */
    private $shopEmail = '';

    private $storeEmail = '';
    /**
     * @var bool Flag if multimerchant option is enabled
     */
    private $multimerchant = false;

    /**
     * @var string Payment API version
     */
    private $api = 'dev';

    /**
     * @var array Array of validation errors with provided data
     */
    private $errors = [];


    private $controlDefault = false;

    /**
     * Create the model based on data provided from shop.
     *
     * @param ConfigurationProviderInterface $provider Provider which contains data from shop application
     *
     * @return Configuration
     */
    public static function createFromData(ConfigurationProviderInterface $provider)
    {
        $configuration = new static($provider->getPluginId());
        $configuration->setEnable($provider->getEnable())
            ->setId($provider->getId())
            ->setPin($provider->getPin())
            ->setUsername($provider->getUsername())
            ->setPassword($provider->getPassword())
            ->setTestMode($provider->getTestMode())
            ->setOcVisible($provider->getOcVisible())
            ->setFccVisible($provider->getFccVisible())
            ->setFccId($provider->getFccId())
            ->setFccPin($provider->getFccPin())
            ->setFccCurrencies($provider->getFccCurrencies())
            ->setCcVisible($provider->getCcVisible())
            ->setMpVisible($provider->getMpVisible())
            ->setBlikVisible($provider->getBlikVisible())
            ->setOtherChannels($provider->getOtherChannels())
            ->setOtherChannelsVisible($provider->getOtherChannelsVisible())
            ->setWidgetVisible($provider->getWidgetVisible())
            ->setWidgetCurrencies($provider->getWidgetCurrencies())
            ->setInstructionVisible($provider->getInstructionVisible())
            ->setRefundsEnable($provider->getRefundsEnable())
            ->setRenew($provider->getRenew())
            ->setRenewDays($provider->getRenewDays())
            ->setSurchargeAmount($provider->getSurchargeAmount())
            ->setSurchargePercent($provider->getSurchargePercent())
            ->setShopName($provider->getShopName())
            ->setShopEmail($provider->getShopEmail())
            ->setMultimerchant($provider->getMultimerchant())
            ->setStoreName($provider->getStoreName())
            ->setStoreEmail($provider->getStoreEmail())
            ->setControlDefault($provider->getControlDefault())
            ->setApi($provider->getApi());

        return $configuration;
    }

    /**
     * Initialize the model.
     *
     * @param string $pluginId Name of the plugin which uses the Configuration
     */
    public function __construct($pluginId)
    {
        $this->setPluginId($pluginId);
    }

    /**
     * Return plugin id.
     *
     * @return string
     */
    public function getPluginId()
    {
        return $this->pluginId;
    }

    /**
     * Return an information if Dotpay payment is enabled on the shop site.
     *
     * @return bool
     */
    public function getEnable()
    {
        return $this->enable;
    }

    /**
     * Return seller id.
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return seller pin.
     *
     * @return string
     */
    public function getPin()
    {
        return $this->pin;
    }

    /**
     * Return username of Dotpay seller dashboard.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Return password of Dotpay seller dashboard.
     *
     * @return password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Check if seller id and pin are not empty.
     *
     * @return bool
     */
    public function isGoodAccount()
    {
        return !(empty($this->id) || empty($this->pin));
    }

    /**
     * Check if username and password are not empty.
     *
     * @return bool
     */
    public function isGoodApiData()
    {
        return !(empty($this->username) || empty($this->password));
    }

    /**
     * Check if test mode is enabled.
     *
     * @return bool
     */
    public function getTestMode()
    {
        return $this->testMode;
    }

    /**
     * Check if the One Click card channel is set as visible.
     *
     * @return bool
     */
    public function getOcVisible()
    {
        return $this->ocVisible;
    }

    /**
     * Check if the One Click card channel is enabled to use.
     *
     * @return bool
     */
    public function isOcEnable()
    {
        return $this->getOcVisible() &&
            !(empty($this->username) &&
                empty($this->password));
    }

    /**
     * Check if card channel for foreign currency is set as visible.
     *
     * @return bool
     */
    public function getFccVisible()
    {
        return $this->fccVisible;
    }

    /**
     * Return seller id for the account which is asigned to card channel for foreign currency.
     *
     * @return int|null
     */
    public function getFccId()
    {
        return $this->fccId;
    }

    /**
     * Return seller pin for the account which is asigned to card channel for foreign currency.
     *
     * @return string
     */
    public function getFccPin()
    {
        return $this->fccPin;
    }

    /**
     * Return a string which contains a list with currency codes for which card channel for foreign currencies is enabled.
     *
     * @return string
     */
    public function getFccCurrencies()
    {
        return $this->fccCurrencies;
    }

    /**
     * Check if card channel for foreign currencies is enabled.
     *
     * @return bool
     */
    public function isFccEnable()
    {
        return $this->getFccVisible() &&
            !(empty($this->fccId) &&
                empty($this->fccPin) &&
                empty($this->fccCurrencies));
    }

    /**
     * Check if normal card channel is set as visible.
     *
     * @return bool
     */
    public function getCcVisible()
    {
        return $this->ccVisible;
    }

    /**
     * Check if MasterPass channel is set as visible.
     *
     * @return bool
     */
    public function getMpVisible()
    {
        return $this->mpVisible;
    }

    /**
     * Check if BLIK channel is set as visible.
     *
     * @return bool
     */
    public function getBlikVisible()
    {
        return $this->blikVisible;
    }

    /**
     * Check if Paypal channel is set as visible.
     *
     * @return bool
     */
    public function getPaypalVisible()
    {
        return $this->paypalVisible;
    }

    /**
     * Check if Dotpay widget is set as visible.
     *
     * @return bool
     */
    public function getWidgetVisible()
    {
        return $this->widgetVisible;
    }

    /**
     * Check if other channels are visible.
     *
     * @return bool
     */
    public function getOtherChannelsVisible()
    {
        return $this->otherChannelsVisible;
    }

    /**
     * Check which other channels are visible.
     *
     * @return bool
     */
    public function getOtherChannels()
    {
        return $this->otherChannels;
    }

    /**
     * Return a string which contains a list with currency codes for which main Dotpay channel is disabled.
     *
     * @return string
     */
    public function getWidgetCurrencies()
    {
        return $this->widgetCurrencies;
    }

    /**
     * Check if payment instruction of cash or transfer channels should be visible on a shop site.
     *
     * @return bool
     */
    public function getInstructionVisible()
    {
        return $this->instructionVisible;
    }

    /**
     * Check if payment instruction of cash or transfer channels should be visible on a shop site.
     *
     * @return bool
     */
    public function getControlDefault()
    {
        return $this->controlDefault;
    }

    /**
     * Check if refunds requesting is enabled from a shop system.
     *
     * @return bool
     */
    public function getRefundsEnable()
    {
        return $this->refundsEnable;
    }

    /**
     * Check if payments renew option is enabled.
     *
     * @return bool
     */
    public function getRenew()
    {
        return $this->renew;
    }

    /**
     * Return a number of days after creating an order when payment can be renewed.
     *
     * @return int
     */
    public function getRenewDays()
    {
        return $this->renewDays;
    }

    /**
     * Return a flag if special surcharge is enabled.
     *
     * @return bool
     */
    public function getSurcharge()
    {
        return $this->surcharge;
    }

    /**
     * Return an amount which will be added as a surcharge.
     *
     * @return float
     */
    public function getSurchargeAmount()
    {
        return $this->surchargeAmount;
    }

    /**
     * Return a percent of value of order which will be added as a surcharge.
     *
     * @return float
     */
    public function getSurchargePercent()
    {
        return $this->surchargePercent;
    }

    /**
     * Check if opayment of rder placed on a given date can be renewed.
     * If number of days is 0, then payment of order can be renewed always.
     *
     * @param DateTime $orderAddDate A date when an order has been placed
     *
     * @return bool
     */
    public function ifOrderCanBeRenewed(DateTime $orderAddDate)
    {
        $now = new DateTime();
        $numberOfRenewDays = $this->getRenewDays();

        return $numberOfRenewDays == 0 || ($orderAddDate < $now && $now->diff($orderAddDate)->format('%a') < $numberOfRenewDays);
    }

    /**
     * Return a name of shop which is sent to Dotpay server.
     *
     * @return string
     */
    public function getShopName()
    {
        return $this->shopName;
    }

    public function getStoreName()
    {
        return $this->storeName;
    }

    /**
     * Return a email of shop which is sent to Dotpay server.
     *
     * @return string
     */
    public function getShopEmail()
    {
        return $this->shopEmail;
    }

    public function getStoreEmail()
    {
        return $this->storeEmail;
    }

    /**
     * Return a flag if multimerchant is enabled.
     *
     * @return bool
     */
    public function getMultimerchant()
    {
        return $this->multimerchant;
    }

    /**
     * Return a payment API version.
     *
     * @return string
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * Return an URL to Dotpay server for payments.
     *
     * @return string
     */
    public function getPaymentUrl()
    {
        if (!$this->getTestMode()) {
            return self::PAYMENT_URL_PROD;
        } else {
            return self::PAYMENT_URL_DEV;
        }
    }

    /**
     * Return an URL to Dotpay server for seller API.
     *
     * @return string
     */
    public function getSellerUrl()
    {
        if (!$this->getTestMode()) {
            return self::SELLER_URL_PROD;
        } else {
            return self::SELLER_URL_DEV;
        }
    }

    /**
     * Check if Dotpay payments support the given currency.
     *
     * @param string $currency Currency code
     *
     * @return bool
     */
    public function isGatewayEnabled($currency)
    {
        return $this->isCurrencyOnList($currency, implode(',', self::$CURRENCIES));
    }

    /**
     * Check if card channel for foreigner currencies can be used for the given currency.
     *
     * @param string $currency Currency code
     *
     * @return bool
     */
    public function isCurrencyForFcc($currency)
    {
        return $this->isCurrencyOnList($currency, $this->getFccCurrencies());
    }

    /**
     * Check if Dotpay widget can be used for the given currency.
     *
     * @param string $currency Currency code
     *
     * @return bool
     */
    public function isWidgetEnabled($currency)
    {
        return !$this->isCurrencyOnList($currency, $this->getWidgetCurrencies());
    }

    /**
     * Return a shop IP or null if it's not possible to read.
     *
     * @return string|null
     */
    public function getShopIp()
    {
        $ip = null;
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } elseif (function_exists('php_sapi_name') && php_sapi_name() == 'cli') {
            $ip = gethostbyname(gethostname());
        }

        return $ip;
    }

    /**
     * Return Seller object for given seller id.
     *
     * @param int $sellerId Seller identifier
     *
     * @return Seller
     *
     * @throws SellerNotFoundException Thrown when seller with the given id is not found in shop configuration
     */
    public function getSeller($sellerId)
    {
        switch ($sellerId) {
            case $this->getId():
                return new Seller($this->getId(), $this->getPin(), $this->getTestMode());
            case $this->getFccId():
                return new Seller($this->getFccId(), $this->getFccPin(), $this->getTestMode());
            default:
                $this->addError(new SellerNotFoundException($sellerId));
        }
    }

    /**
     * Return list of channels which are enabled in configuration.
     *
     * @return array/null
     */
    public function getEnabledChannels()
    {
        $channels = [];
        if ($this->isOcEnable()) {
            $channels[] = self::OC_CHANNEL;
        }
        if ($this->isFccEnable()) {
            $channels[] = self::FCC_CHANNEL;
            $channels[] = self::CC_CHANNEL;
        }
        if ($this->getCcVisible()) {
            $channels[] = self::CC_CHANNEL;
            $channels[] = self::FCC_CHANNEL;
        }
        if ($this->getBlikVisible()) {
            $channels[] = self::BLIK_CHANNEL;
        }
        if ($this->getMpVisible()) {
            $channels[] = self::MP_CHANNEL;
        }
        if ($this->getPaypalVisible()) {
            $channels[] = self::PAYPAL_CHANNEL;
        }
        if (count($this->otherChannels) > 0)
        {
            foreach($this->otherChannels as $ch)
            {
                if(is_numeric($ch) && $ch > 0) {
                    $channels[] = (int) $ch;
                }
            }
        }
        if (count($channels)) {

            return $channels;
        } else {
            return null;
        }
    }

    /**
     * Set the given plugin id.
     *
     * @param string $pluginId Plugin id
     *
     * @return Configuration
     */
    public function setPluginId($pluginId)
    {
        $this->pluginId = (string) $pluginId;

        return $this;
    }

    /**
     * Set the flag if Dotpay payment is enabled in a shop.
     *
     * @param bool $enable Flag of enabling Dotpay payment
     *
     * @return Configuration
     */
    public function setEnable($enable)
    {
        $this->enable = (bool) $enable;

        return $this;
    }

    /**
     * Set the given seller id.
     *
     * @param int $id Seller id
     *
     * @return Configuration
     *
     * @throws IdException Thrown when the given seller id is incorrect
     */
    public function setId($id)
    {
        if (!Id::validate($id) && ($this->getEnable() || !empty($id))) {
            $this->addError(new IdException($id));
            return $this;
        }
        $this->id = (int) $id;

        return $this;
    }

    /**
     * Set the given seller pin.
     *
     * @param string $pin Seller pin
     *
     * @return Configuration
     *
     * @throws PinException Thrown when the given seller pin is incorrect
     */
    public function setPin($pin)
    {
        if (!Pin::validate($pin) && ($this->getEnable() || !empty($pin))) {
            $this->addError(new PinException($pin));
            return $this;
        }
        $this->pin = (string) $pin;

        return $this;
    }

    /**
     * Set the given username for Dotpay dashboard.
     *
     * @param string $username Seller username
     *
     * @return Configuration
     *
     * @throws UsernameException Thrown when the given username is incorrect
     */
    public function setUsername($username)
    {
        if (!empty($username) && !Username::validate($username)) {
            $this->addError(new UsernameException($username));
            return $this;
        }
        $this->username = (string) $username;

        return $this;
    }

    /**
     * Set the given password for Dotpay dashboard.
     *
     * @param string $password Seller password
     *
     * @return Configuration
     *
     * @throws PasswordException Thrown when the given password is incorrect
     */
    public function setPassword($password)
    {
        if (!empty($password) && empty($password)) {
            $this->addError(new PasswordException());
            return $this;
        }
        $this->password = (string) $password;

        return $this;
    }

    /**
     * Set the flag which informs if test mode is enabled or not.
     *
     * @param bool $testMode Test mode flag
     *
     * @return Configuration
     */
    public function setTestMode($testMode)
    {
        $this->testMode = (bool) $testMode;

        return $this;
    }

    /**
     * Set the flag which informs if One Click card channel is visible.
     *
     * @param bool $ocVisible One Click card channel visible flag
     *
     * @return Configuration
     */
    public function setOcVisible($ocVisible)
    {
        $this->ocVisible = (bool) $ocVisible;

        return $this;
    }

    /**
     * Set the flag which informs if card channel for foreign currencies is visible.
     *
     * @param bool $fccVisible Card channel for foreign currencies visible flag
     *
     * @return Configuration
     */
    public function setFccVisible($fccVisible)
    {
        $this->fccVisible = (bool) $fccVisible;

        return $this;
    }

    /**
     * Set the flag which informs if other channels are visible.
     *
     * @param bool $otherChannelsVisible other channles visible flag
     *
     * @return Configuration
     */
    public function setOtherChannelsVisible($otherChannelsVisible)
    {
        $this->otherChannelsVisible = (bool) $otherChannelsVisible;

        return $this;
    }

    /**
     * Set the list of channels out of widget.
     *
     * @param array $otherChannels ids of channels put out of widget
     *
     * @return Configuration
     */
    public function setOtherChannels($otherChannels)
    {
        $this->otherChannels = $otherChannels;

        return $this;
    }

    /**
     * Set the given seller id for the second account.
     *
     * @param int $fccId Seller id for an account which is signed to support payment by card using foreign currencies
     *
     * @return Configuration
     *
     * @throws IdException Thrown when the given seller id for the second account is incorrect
     */
    public function setFccId($fccId)
    {
        if ($this->getFccVisible()) {
            if (!empty($fcc) && !Id::validate($fccId)) {
                $this->addError(new FccIdException($fccId));
                return $this;
            }
            $this->fccId = (int) $fccId;
        }

        return $this;
    }

    /**
     * Set the given seller pin for the second account.
     *
     * @param string $fccPin Seller pin for an account which is signed to support payment by card using foreign currencies
     *
     * @return Configuration
     *
     * @throws PinException Thrown when the given seller pin is incorrect
     */
    public function setFccPin($fccPin)
    {
        if ($this->getFccVisible()) {
            if (!empty($fccPin) && !Pin::validate($fccPin)) {
                $this->addError(new FccPinException($fccPin));
                return $this;
            }
            $this->fccPin = (string) $fccPin;
        }

        return $this;
    }

    /**
     * Set the list of codes of currencies for which is allowed the FCC card channel.
     *
     * @param string $fccCurrencies List of codes of currencies for which is allowed the FCC card channel.
     *                              Every code is separated by "," character
     *
     * @return Configuration
     */
    public function setFccCurrencies($fccCurrencies)
    {
        if ($this->getFccVisible()) {
            $this->fccCurrencies = strtoupper($fccCurrencies);
        }

        return $this;
    }

    /**
     * Set the flag if normal card channel is visible.
     *
     * @param bool $ccVisible Flag if normal card channel is visible
     *
     * @return Configuration
     */
    public function setCcVisible($ccVisible)
    {
        $this->ccVisible = (bool) $ccVisible;

        return $this;
    }

    /**
     * Set the flag if MasterPass channel is visible.
     *
     * @param bool $mpVisible Flag if MasterPass channel is visible
     *
     * @return Configuration
     */
    public function setMpVisible($mpVisible)
    {
        $this->mpVisible = (bool) $mpVisible;

        return $this;
    }

    /**
     * Set the flag if BLIK channel is visible.
     *
     * @param bool $blikVisible Flag if BLIK channel is visible
     *
     * @return Configuration
     */
    public function setBlikVisible($blikVisible)
    {
        $this->blikVisible = (bool) $blikVisible;

        return $this;
    }

    /**
     * Set the flag if Paypal channel is visible.
     *
     * @param bool $paypalVisible Flag if Paypal channel is visible
     *
     * @return Configuration
     */
    public function setPaypalVisible($paypalVisible)
    {
        $this->paypalVisible = (bool) $paypalVisible;

        return $this;
    }

    /**
     * Set the flag if Dotpay widget is visible on a payment page.
     *
     * @param bool $widgetVisible Flag if Dotpay widget is visible on a payment page
     *
     * @return Configuration
     */
    public function setWidgetVisible($widgetVisible)
    {
        $this->widgetVisible = (bool) $widgetVisible;

        return $this;
    }

    /**
     * Set the list of currency codes for which is disallowed the Dotpay main channel.
     *
     * @param string $widgetCurrencies List of currency codes.
     *                                 Every code is separated by "," character
     *
     * @return Configuration
     */
    public function setWidgetCurrencies($widgetCurrencies)
    {
        $this->widgetCurrencies = strtoupper($widgetCurrencies);

        return $this;
    }

    /**
     * Set a flag if payment instruction of cash or transfer channels should be visible on a shop site.
     *
     * @param bool $instructionVisible Flag if payment instruction of cash or transfer channels should be visible on a shop site
     *
     * @return Configuration
     */
    public function setInstructionVisible($instructionVisible)
    {
        $this->instructionVisible = (bool) $instructionVisible;

        return $this;
    }

    /**
     * Set a flag if Control field with additional information (default)
     *
     * @param bool $controlDefault Flag if Control field with additional information (default)
     *
     * @return Configuration
     */
    public function setControlDefault($controlDefault)
    {
        $this->controlDefault = (bool) $controlDefault;

        return $this;
    }

    /**
     * Set a flag if refunds requesting is enabled from a shop system.
     *
     * @param bool $refundsEnable Flag of refunds enabling from shop sites
     *
     * @return Configuration
     */
    public function setRefundsEnable($refundsEnable)
    {
        $this->refundsEnable = (bool) $refundsEnable;

        return $this;
    }

    /**
     * Set a flag if payment of order can be renewed.
     *
     * @param bool $renew
     *
     * @return Configuration
     */
    public function setRenew($renew)
    {
        $this->renew = (bool) $renew;

        return $this;
    }

    /**
     * Set a number of days when after placed an order the payment can be renewed.
     *
     * @param type $renewDays
     *
     * @return Configuration
     */
    public function setRenewDays($renewDays)
    {
        if ($this->getRenew()) {
            if (!empty($renewDays)) {
                $this->renewDays = (int) $renewDays;
            } else {
                $this->renewDays = 0;
            }
        }

        return $this;
    }

    /**
     * Set a flag if special surcharge is enabled.
     *
     * @param bool $surcharge Flag if special surcharge is enabled
     *
     * @return Configuration
     */
    public function setSurcharge($surcharge)
    {
        $this->surcharge = (bool) $surcharge;

        return $this;
    }

    /**
     * Set an amount which will be added as a surcharge.
     *
     * @param float $surchargeAmount Amount which will be added as a surcharge
     *
     * @return Configuration
     */
    public function setSurchargeAmount($surchargeAmount)
    {
        $this->surchargeAmount = $surchargeAmount;

        return $this;
    }

    /**
     * Set a percent of value of order which will be added as a surcharge.
     *
     * @param float $surchargePercent Percent of value of order which will be added as a surcharge
     *
     * @return Configuration
     */
    public function setSurchargePercent($surchargePercent)
    {
        $this->surchargePercent = $surchargePercent;

        return $this;
    }

    /**
     * Set the given name of shop which is sent to Dotpay server.
     *
     * @param string $shopName Shop name
     *
     * @return Configuration
     */
    public function setShopName($shopName)
    {
        $this->shopName = (string) $shopName;

        return $this;
    }

    public function setStoreName($storeName)
    {
        $this->storeName = (string) $storeName;

        return $this;
    }

    /**
     * Set the given email of shop which is sent to Dotpay server.
     *
     * @param string $shopEmail Shop email
     *
     * @return Configuration
     */
    public function setShopEmail($shopEmail)
    {
        $this->shopEmail = (string) $shopEmail;

        return $this;
    }
    
    public function setStoreEmail($storeEmail)
    {
        $this->storeEmail = (string) $storeEmail;

        return $this;
    }

    /**
     * Set a flag if multimerchant option is enabled.
     *
     * @param bool $multimerchant Flag if multimerchant option is enabled
     *
     * @return Configuration
     */
    public function setMultimerchant($multimerchant)
    {
        $this->multimerchant = (bool) $multimerchant;

        return $this;
    }

    /**
     * Set the given API version.
     *
     * @param string $api Api version. Only "dev" is allowed
     *
     * @return Configuration
     *
     * @throws ApiVersionException Thrown when the given payment API version is different than the "dev" string
     */
    public function setApi($api)
    {
        if ($api !== 'dev') {
            $this->addError(new ApiVersionException($api));
            return $this;
        }
        $this->api = $api;

        return $this;
    }

    /**
     * Check if payment module is activated
     *
     * @return boolean
     */
    public function isActivated()
    {
        return $this->getEnable() && Id::validate($this->getId()) && Pin::validate($this->getPin());
    }

    /**
     * Check if the given currency is on the given list.
     *
     * @param string       $currency Currency code
     * @param string/array $list     A string which contains a list of currency codes. Every code is separated by "," character
     *
     * @return booleanean
     */
    private function isCurrencyOnList($currency, $list)
    {
        $result = false;
        if (is_array($list)) {
            $allowCurrencyArray = $list;
        } else {
            $allowCurrency = str_replace(';', ',', $list);
            $allowCurrency = strtoupper(str_replace(' ', '', $allowCurrency));
            $allowCurrencyArray = explode(',', trim($allowCurrency));
        }
        if (in_array(strtoupper($currency), $allowCurrencyArray)) {
            $result = true;
        }

        return $result;
    }

    /**
     * Add an Exception to errors array
     *
     * @param \Exception $e Exception to be added
     *
     * @return Configuration
     */
    public function addError($e)
    {
        $this->errors[$e->getMessage()] = $e;

        return $this;
    }

    /**
     * Return aa array of validation errors.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Set a private property from child class.
     *
     * @param string $name  Name of the property
     * @param mixed  $value Value of the property
     */
    public function __set($name, $value)
    {
        $this->$name = $value;
    }
}
