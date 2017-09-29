<?php
/**
 * Copyright (c) 2017 Dotpay S.A. <techdotpay.pl>.
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
 * @copyright Dotpay S.A
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */

namespace Dotpay\Provider;

/**
 * Interface of notification data providers from shop.
 */
interface NotificationProviderInterface
{
    /**
     * Return an Operation object with details of operation which relates the notification.
     *
     * @return \Dotpay\Model\Operation
     */
    public function getOperation();

    /**
     * Return an email of a seller.
     *
     * @return string
     */
    public function getShopEmail();

    /**
     * Return a name of a shop.
     *
     * @return string
     */
    public function getShopName();

    /**
     * Return an id of used payment channel.
     *
     * @return int
     */
    public function getChannelId();

    /**
     * Return a codename of a country of the payment instrument from which payment was made.
     *
     * @return string
     */
    public function getChannelCountry();

    /**
     * Return a codename of a country resulting from IP address from which the payment was made.
     *
     * @return string
     */
    public function getIpCountry();

    /**
     * Return a checksum of a Dotpay notification.
     *
     * @return string
     */
    public function getSignature();
}
