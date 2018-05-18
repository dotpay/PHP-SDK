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

namespace Dotpay\Resource\RegisterOrder;

use Dotpay\Validator\Url;
use Dotpay\Model\Redirect;
use Dotpay\Model\Operation;
use Dotpay\Model\Instruction;
use Dotpay\Exception\BadParameter\UrlException;

/**
 * Result of the operation which was realized through the Register Order method.
 */
class Result
{
    /**
     * @var string Url of the page where it's possible to check a status of the payment
     */
    private $statusUrl;

    /**
     * @var Redirect Object which contains informations about redirecting to the page where the payment can be finished, if it's needed by the used payment channel
     */
    private $redirect = null;

    /**
     * @var Operation Details of the realized operation
     */
    private $operation;

    /**
     * @var Instruction Details of instruction how it's possible to finish the realized payment, if it's needed by the used payment channel
     */
    private $instruction = null;

    /**
     * Prepare basic informations about the result of operation.
     *
     * @param string    $statusUrl Url of the page where it's possible to check a status of the payment
     * @param Operation $operation Details of the realized operation
     */
    public function __construct($statusUrl, Operation $operation)
    {
        $this->setStatusUrl($statusUrl);
        $this->setOperation($operation);
    }

    /**
     * Return an url of the page where it's possible to check a status of the payment.
     *
     * @return string
     */
    public function getStatusUrl()
    {
        return $this->statusUrl;
    }

    /**
     * Return an object which contains informations about redirecting to the finish page.
     *
     * @return Redirect|null
     */
    public function getRedirect()
    {
        return $this->redirect;
    }

    /**
     * Return details of the realized operation.
     *
     * @return Operation
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * Return details of instruction how it's possible to finish the realized payment.
     *
     * @return Instruction|null
     */
    public function getInstruction()
    {
        return $this->instruction;
    }

    /**
     * Set an url of the page where it's possible to check a status of the payment.
     *
     * @param string $statusUrl Url of the page where it's possible to check a status of the payment
     *
     * @return Result
     *
     * @throws UrlException Thrown when the given url is incorrect
     */
    public function setStatusUrl($statusUrl)
    {
        if (!Url::validate($statusUrl)) {
            throw new UrlException($statusUrl);
        }
        $this->statusUrl = $statusUrl;

        return $this;
    }

    /**
     * Set an object which contains informations about redirecting to the finish page.
     *
     * @param Redirect $redirect Object which contains informations about redirecting to the page where the payment can be finished
     *
     * @return Result
     */
    public function setRedirect(Redirect $redirect)
    {
        $this->redirect = $redirect;

        return $this;
    }

    /**
     * Set details of the realized operation.
     *
     * @param Operation $operation Details of the realized operation
     *
     * @return Result
     */
    public function setOperation(Operation $operation)
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * Set details of instruction how it's possible to finish the realized payment.
     *
     * @param Instruction $instruction Details of instruction how it's possible to finish the realized payment
     *
     * @return Result
     */
    public function setInstruction(Instruction $instruction)
    {
        $this->instruction = $instruction;

        return $this;
    }
}
