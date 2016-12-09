<?php

namespace Dotpay\Model;

use Dotpay\Validator\Id;
use Dotpay\Validator\Pin;
use Dotpay\Validator\Username;
use Dotpay\Exception\BadParameter\IdException;
use Dotpay\Exception\BadParameter\PinException;
use Dotpay\Exception\BadParameter\UsernameException;
use Dotpay\Exception\BadParameter\ApiVersionException;

class Configuration {
    const paymentUrlProd = 'https://ssl.dotpay.pl/t2/';
    const paymentUrlDev = 'https://ssl.dotpay.pl/test_payment/';
    const sellerUrlProd = 'https://ssl.dotpay.pl/s2/login/';
    const sellerUrlDev = 'https://ssl.dotpay.pl/test_seller/';
    
    const callbackIp = '195.150.9.37';
    const officeIp = '77.79.195.34';
    const localIp = '127.0.0.1';
    
    const ocChannel = 248;
    const fccChannel = 248;
    const ccChannel = 246;
    const mpChannel = 71;
    const blikChannel = 73;
    
    const cashGroup = '';
    const transferGroup = '';
    
    const widgetClassContainer = 'dotpay-widget-container';
    
    const CURRENCIES = array(
        'EUR',
        'USD',
        'GBP',
        'JPY',
        'CZK',
        'SEK',
        'PLN'
    );
    
    private $enable;
    private $id;
    private $pin;
    private $username;
    private $password;
    private $testMode;
    private $ocVisible;
    private $fccVisible;
    private $fccId;
    private $fccPin;
    private $fccCurrencies;
    private $ccVisible;
    private $mpVisible;
    private $blikVisible;
    private $widgetVisible;
    private $widgetCurrencies;
    private $instructionVisible;
    private $shopName;
    
    private $api = 'dev';
    
    public function getEnable() {
        return $this->enable;
    }
    
    public function getId() {
        return $this->id;
    }

    public function getPin() {
        return $this->pin;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }
    
    public function isGoodAccount() {
        return !(empty($this->id) || empty($this->pin));
    }
    
    public function isGoodApiData() {
        return !(empty($this->username) || empty($this->password));
    }

    public function getTestMode() {
        return $this->testMode;
    }

    public function getOcVisible() {
        return $this->ocVisible;
    }
    
    public function isOcEnable() {
        return $this->getOcVisible() && 
               !(empty($this->username) && 
                 empty($this->password));
    }

    public function getFccVisible() {
        return $this->fccVisible;
    }

    public function getFccId() {
        return $this->fccId;
    }

    public function getFccPin() {
        return $this->fccPin;
    }

    public function getFccCurrencies() {
        return $this->fccCurrencies;
    }
    
    public function isFccEnable() {
        return $this->getFccVisible() && 
               !(empty($this->fccId) && 
                 empty($this->fccPin) && 
                 empty($this->fccCurrencies));
    }

    public function getCcVisible() {
        return $this->ccVisible;
    }

    public function getMpVisible() {
        return $this->mpVisible;
    }

    public function getBlikVisible() {
        return $this->blikVisible;
    }

    public function getWidgetVisible() {
        return $this->widgetVisible;
    }
    
    public function getWidgetCurrencies() {
        return $this->widgetCurrencies;
    }
    
    public function getInstructionVisible() {
        return $this->instructionVisible;
    }
    
    public function getShopName() {
        return $this->shopName;
    }
    
    public function getApi() {
        return $this->api;
    }
    
    public function getPaymentUrl() {
        if(!$this->getTestMode())
            return self::paymentUrlProd;
        else
            return self::paymentUrlDev;
    }
    
    public function getSellerUrl() {
        if(!$this->getTestMode())
            return self::sellerUrlProd;
        else
            return self::sellerUrlDev;
    }
    
    public function isGatewayEnabled($currency) {
        return $this->isCurrencyOnList($currency, implode(',', self::CURRENCIES));
    }
    
    public function isCurrencyForFcc($currency) {
        return $this->isCurrencyOnList($currency, $this->getFccCurrencies());
    }
    
    public function isWidgetEnabled($currency) {
        return !$this->isCurrencyOnList($currency, $this->getWidgetCurrencies());
    }
    
    public function getShopIp() {
        $ip = null;
        if(isset($_SERVER['REMOTE_ADDR']))
            $ip = $_SERVER['REMOTE_ADDR'];
        else if(function_exists('php_sapi_name') && php_sapi_name() == 'cli')
            $ip = gethostbyname(gethostname());
        return $ip;
    }
    
    public function setEnable($enable) {
        $this->enable = (bool)$enable;
        return $this;
    }

    public function setId($id) {
        if(!Id::validate($id))
            throw new IdException($id);
        $this->id = $id;
        return $this;
    }

    public function setPin($pin) {
        if(!Pin::validate($pin))
            throw new PinException($pin);
        $this->pin = $pin;
        return $this;
    }

    public function setUsername($username) {
        if(!Username::validate($username))
            throw new UsernameException($username);
        $this->username = $username;
        return $this;
    }

    public function setPassword($password) {
        if(empty($password))
            throw new PasswordException();
        $this->password = $password;
        return $this;
    }

    public function setTestMode($testMode) {
        $this->testMode = (bool)$testMode;
        return $this;
    }

    public function setOcVisible($ocVisible) {
        $this->ocVisible = (bool)$ocVisible;
        return $this;
    }

    public function setFccVisible($pvVisible) {
        $this->fccVisible = (bool)$pvVisible;
        return $this;
    }

    public function setFccId($pvId) {
        if(!Id::validate($pvId))
            throw new IdException($pvId);
        $this->fccId = $pvId;
        return $this;
    }

    public function setFccPin($pvPin) {
        if(!Pin::validate($pvPin))
            throw new PinException($pvPin);
        $this->fccPin = $pvPin;
        return $this;
    }

    public function setFccCurrencies($pvCurrencies) {
        $this->fccCurrencies = strtoupper($pvCurrencies);
        return $this;
    }

    public function setCcVisible($ccVisible) {
        $this->ccVisible = (bool)$ccVisible;
        return $this;
    }

    public function setMpVisible($mpVisible) {
        $this->mpVisible = (bool)$mpVisible;
        return $this;
    }

    public function setBlikVisible($blikVisible) {
        $this->blikVisible = (bool)$blikVisible;
        return $this;
    }

    public function setWidgetVisible($widgetVisible) {
        $this->widgetVisible = (bool)$widgetVisible;
        return $this;
    }
    
    public function setWidgetCurrencies($widgetCurrencies) {
        $this->widgetCurrencies = strtoupper($widgetCurrencies);
        return $this;
    }
    
    public function setInstructionVisible($instructionVisible) {
        $this->instructionVisible = (bool)$instructionVisible;
        return $this;
    }
    
    public function setShopName($shopName) {
        $this->shopName = $shopName;
        return $this;
    }
    
    public function setApi($api) {
        if($api !== 'dev')
            throw new ApiVersionException($api);
        $this->api = $api;
        return $this;
    }

    public function setInitialState() {
        $this->enable = false;
        $this->id = '';
        $this->pin = '';
        $this->username = '';
        $this->password = '';
        $this->testMode = false;
        $this->ocVisible = false;
        $this->fccVisible = false;
        $this->fccId = '';
        $this->fccPin = '';
        $this->fccCurrencies = '';
        $this->ccVisible = false;
        $this->mpVisible = false;
        $this->blikVisible = false;
        $this->widgetVisible = true;
        $this->widgetCurrencies = '';
        $this->instructionVisible = true;
        return $this;
    }
    
    private function isCurrencyOnList($currency, $list) {
        $result = false;

        $allowCurrency = str_replace(';', ',', $list);
        $allowCurrency = strtoupper(str_replace(' ', '', $allowCurrency));
        $allowCurrencyArray =  explode(",",trim($allowCurrency));
        
        if(in_array(strtoupper($currency), $allowCurrencyArray)) {
            $result = true;
        }
        
        return $result;
    }
}