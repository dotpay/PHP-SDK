<?php
/**
 * Copyright (c) 2017 Dotpay S.A. <tech@dotpay.pl>.
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

namespace Dotpay\Model;

use Dotpay\Validator\ChannelId;
use Dotpay\Exception\BadParameter\ChannelIdException;

/**
 * Informations about a payment method.
 */
class PaymentMethod
{
    /**
     * @var int|null Payment channel id
     */
    private $channelId = null;

    /**
     * @var mixed Details of payment method
     */
    private $details = null;

    /**
     * @var int|null Type of defails of payment method
     */
    private $detailsType = null;

    /**
     * Details with bank account.
     */
    const BANK_ACCOUNT = 1;

    /**
     * Details with credit card.
     */
    const CREDIT_CARD = 2;

    /**
     * Initialize the model.
     *
     * @param int      $channelId   Payment channel id
     * @param mixed    $details     Details of payment method
     * @param int|null $detailsType Type of defails of payment method
     */
    public function __construct($channelId, $details = null, $detailsType = null)
    {
        $this->setChannelId($channelId);
        $this->setDetails($details);
        $this->setDetailsType($detailsType);
    }

    /**
     * Return a payment channel id.
     *
     * @return int|null
     */
    public function getChannelId()
    {
        return $this->channelId;
    }

    /**
     * Return a details of payment method.
     *
     * @return mixed
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Return a type of defails of payment method.
     *
     * @return int|null
     */
    public function getDetailsType()
    {
        return $this->detailsType;
    }

    /**
     * Set a payment channel id.
     *
     * @param type $channelId Payment channel id
     *
     * @return PaymentMethod
     *
     * @throws ChannelIdException Thrown when the given channel id is incorrect
     */
    public function setChannelId($channelId)
    {
        if (!ChannelId::validate($channelId)) {
            throw new ChannelIdException($channelId);
        }
        $this->channelId = (int) $channelId;

        return $this;
    }

    /**
     * Set a details of payment method.
     *
     * @param mixed $details Details of payment method
     *
     * @return PaymentMethod
     */
    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Set a type of defails of payment method.
     *
     * @param int|null $type Type of defails of payment method
     *
     * @return PaymentMethod
     */
    public function setDetailsType($type)
    {
        $this->detailsType = (int) $type;

        return $this;
    }
}
