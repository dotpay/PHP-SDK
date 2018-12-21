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

namespace Dotpay\Resource;

use Dotpay\Resource\Channel\Info;
use Dotpay\Exception\Resource\ApiException;
use Dotpay\Exception\BadReturn\TypeNotCompatibleException;
use Dotpay\Model\Transaction;
use Dotpay\Resource\Channel\Request;

/**
 * Allow to use informations about channels which are enabled for details of payment.
 */
class Payment extends Resource
{
    /**
     * @var array List of channels lists which are gotten from server for specific orders
     */
    private $buffer;

    /**
     * Return the Info structure which contains list of channels for the given payment details.
     *
     * @param Request $request Details of request information
     *
     * @return Info
     *
     * @throws TypeNotCompatibleException Thrown when a response from Dotpay server is in incompatible type
     * @throws ApiException               Thrown when is reported an Api Error
     */
    public function getChannelListForRequest(Request $request)
    {
        $id = $request->getIdentifier();
        if (!isset($this->buffer[$id])) {
            $content = $this->getContent($request->getUrl());
            if (!is_array($content)) {
                throw new TypeNotCompatibleException(gettype($content));
            }
            if (isset($content['error_code'])) {
                $exception = new ApiException($content['detail']);
                throw $exception->setApiCode($content['error_code']);
            }
            $this->buffer[$id] = new Info($content['channels'], $content['forms']);
            unset($content);
        }

        return $this->buffer[$id];
    }

    /**
     * Return the Info structure which contains list of channels for the given payment details.
     *
     * @param Transaction $transaction Payment details
     *
     * @return Info
     *
     * @throws TypeNotCompatibleException Thrown when a response from Dotpay server is in incompatible type
     * @throws ApiException               Thrown when is reported an Api Error
     */
    public function getChannelListForTransaction(Transaction $transaction)
    {
        return $this->getChannelListForRequest(Request::getFromTransaction($transaction));
    }

    /**
     * Clear the buffer of past requests.
     *
     * @return Payment
     */
    public function clearBuffer()
    {
        unset($this->buffer);
        $this->buffer = [];

        return $this;
    }

    /**
     * Check if the seller with the given id exists in Dotpay system.
     *
     * @param int     $id      Seller id
     * @param Request $request Object with request data. If it's not provided, default request is created automatically
     *
     * @return bool
     *
     * @throws TypeNotCompatibleException Thrown when a response from Dotpay server is in incompatible type
     */
    public function checkSeller($id, Request $request = null)
    {
        if($id === null)
        {
            return false;
        }
        if ($request === null) {
            $request = Request::getFromSellerId($id, $this->config->getTestMode());
        }
        $content = $this->getContent($request->getUrl());
        if (!is_array($content)) {
            throw new TypeNotCompatibleException(gettype($content));
        }
        if (isset($content['error_code']) && $content['error_code'] == 'UNKNOWN_ACCOUNT') {
            unset($content);

            return false;
        }
        unset($content);

        return true;
    }
}
