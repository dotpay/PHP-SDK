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

namespace Dotpay\Processor;

use Dotpay\Exception\FunctionNotFoundException;

/**
 * Processor of checking status of an order.
 */
class Status
{
    /**
     * @var int Order doesn't exist
     */
    private static $NOT_EXISTS = -1;

    /**
     * @var int An error with payment has been occured
     */
    private static $ERROR = 0;

    /**
     * @var int Shop is still waiting for confirmation of payment
     */
    private static $PENDING = 1;

    /**
     * @var int Order has been paid successfully
     */
    private static $SUCCESS = 2;

    /**
     * @var int Order has been paid before
     */
    private static $TOO_MANY = 3;

    /**
     * @var int Status of the order is different than ERROR or PENDING
     */
    private static $OTHER_STATUS = 4;

    /**
     * @var int Status code
     */
    private $code = -1000;

    /**
     * @var string Order status from a shop
     */
    private $status = '';

    /**
     * @var string An additional message which can be displayed on a shop site
     */
    private $message = null;

    /**
     * Return a status code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set a status code as order wasn't found.
     *
     * @return Status
     */
    public function codeNotExist()
    {
        $this->code = self::$NOT_EXISTS;

        return $this;
    }

    /**
     * Set a status code as an error.
     *
     * @return Status
     */
    public function codeError()
    {
        $this->code = self::$ERROR;

        return $this;
    }

    /**
     * Set a status code as a pending.
     *
     * @return Status
     */
    public function codePending()
    {
        $this->code = self::$PENDING;

        return $this;
    }

    /**
     * Set a status code as a success.
     *
     * @return Status
     */
    public function codeSuccess()
    {
        $this->code = self::$SUCCESS;

        return $this;
    }

    /**
     * Set a status code as too many payments.
     *
     * @return Status
     */
    public function codeTooMany()
    {
        $this->code = self::$TOO_MANY;

        return $this;
    }

    /**
     * Set a status code as other status.
     *
     * @return Status
     */
    public function codeOtherStatus()
    {
        $this->code = self::$OTHER_STATUS;

        return $this;
    }

    /**
     * Return an order status description.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the given order status description.
     *
     * @param string $status Order status description
     *
     * @return Status
     */
    public function setStatus($status)
    {
        $this->status = (string) $status;

        return $this;
    }

    /**
     * Return an additional message.
     *
     * @return string|null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set an additional message.
     *
     * @param string $message An additional message
     *
     * @return Status
     */
    public function setMessage($message)
    {
        $this->message = (string) $message;

        return $this;
    }

    /**
     * Return array with status data.
     *
     * @return array
     */
    public function getData()
    {
        $data = [
            'code' => $this->getCode(),
            'status' => $this->getStatus(),
        ];
        if ($this->getMessage() !== null) {
            $data['message'] = $this->getMessage();
        }

        return $data;
    }

    /**
     * Return a string which contains JSON data with the current status information.
     *
     * @return string
     *
     * @throws FunctionNotFoundException Thrown when function json_encode() isn't found
     */
    public function getJson()
    {
        if (function_exists('json_encode')) {
            return json_encode($this->getData(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } else {
            throw new FunctionNotFoundException('json_encode');
        }
    }
}
