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

namespace Dotpay\Provider;

/**
 * Interface of configuration data providers from shop.
 */
interface ConfigurationProviderInterface
{
    /**
     * Return plugin id.
     *
     * @return string
     */
    public function getPluginId();

    /**
     * Return an information if Dotpay payment is enabled on the shop site.
     *
     * @return bool
     */
    public function getEnable();

    /**
     * Return seller id.
     *
     * @return int|null
     */
    public function getId();

    /**
     * Return seller pin.
     *
     * @return string
     */
    public function getPin();

    /**
     * Return username of Dotpay seller dashboard.
     *
     * @return string
     */
    public function getUsername();

    /**
     * Return password of Dotpay seller dashboard.
     *
     * @return password
     */
    public function getPassword();

    /**
     * Check if test mode is enabled.
     *
     * @return bool
     */
    public function getTestMode();

    /**
     * Check if the One Click card channel is set as visible.
     *
     * @return bool
     */
    public function getOcVisible();

    /**
     * Check if the One Click card channel is enabled to use.
     *
     * @return bool
     */
    public function getFccVisible();

    /**
     * Return seller id for the account which is asigned to card channel for foreign currency.
     *
     * @return int|null
     */
    public function getFccId();

    /**
     * Return seller pin for the account which is asigned to card channel for foreign currency.
     *
     * @return string
     */
    public function getFccPin();

    /**
     * Return a string which contains a list with currency codes for which card channel for foreign currencies is enabled.
     *
     * @return string
     */
    public function getFccCurrencies();

    /**
     * Check if normal card channel is set as visible.
     *
     * @return bool
     */
    public function getCcVisible();

    /**
     * Check if MasterPass channel is set as visible.
     *
     * @return bool
     */
    public function getMpVisible();

    /**
     * Check if BLIK channel is set as visible.
     *
     * @return bool
     */
    public function getBlikVisible();

    /**
     * Check if Paypal channel is set as visible.
     *
     * @return bool
     */
    public function getPaypalVisible();

    /**
     * Check if Dotpay widget is set as visible.
     *
     * @return bool
     */
    public function getWidgetVisible();

    /**
     * Return a string which contains a list with currency codes for which main Dotpay channel is disabled.
     *
     * @return string
     */
    public function getWidgetCurrencies();

    /**
     * Check if payment instruction of cash or transfer channels should be visible on a shop site.
     *
     * @return bool
     */
    public function getInstructionVisible();

    /**
     * Check if refunds requesting is enabled from a shop system.
     *
     * @return bool
     */
    public function getRefundsEnable();

    /**
     * Check if payments renew option is enabled.
     *
     * @return bool
     */
    public function getRenew();

    /**
     * Return a number of days after creating an order when payment can be renewed.
     *
     * @return int
     */
    public function getRenewDays();

    /**
     * Return a flag if special surcharge is enabled.
     *
     * @return bool
     */
    public function getSurcharge();

    /**
     * Return an amount which will be added as a surcharge.
     *
     * @return float
     */
    public function getSurchargeAmount();

    /**
     * Return a percent of value of order which will be added as a surcharge.
     *
     * @return float
     */
    public function getSurchargePercent();

    /**
     * Return a name of shop which is sent to Dotpay server.
     *
     * @return string
     */
    public function getShopName();

    /**
     * Return a flag if multimerchant is enabled.
     *
     * @return bool
     */
    public function getMultimerchant();

    /**
     * Return a payment API version.
     *
     * @return string
     */
    public function getApi();
}
