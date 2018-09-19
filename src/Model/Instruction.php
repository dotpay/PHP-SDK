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

namespace Dotpay\Model;

use Dotpay\Provider\InstructionProviderInterface;
use Dotpay\Tool\AmountFormatter;
use Dotpay\Validator\OpNumber;
use Dotpay\Validator\BankNumber;
use Dotpay\Validator\ChannelId;
use Dotpay\Validator\Amount;
use Dotpay\Validator\Currency;
use Dotpay\Exception\BadParameter\AmountException;
use Dotpay\Exception\BadParameter\CurrencyException;
use Dotpay\Exception\BadParameter\OperationNumberException;
use Dotpay\Exception\BadParameter\BankNumberException;
use Dotpay\Exception\BadParameter\ChannelIdException;

/**
 * Informations about an instruction of payments by cash or transfer.
 */
class Instruction
{
    /**
     * Name of the recipient of payment.
     */
    const RECIPIENT_NAME = 'Dotpay sp. z o.o.';

    /**
     * Street of the recipient of payment.
     */
    const RECIPIENT_STREET = 'Wielicka 72';

    /**
     * Post code and city of the recipient of payment.
     */
    const RECIPIENT_CITY = '30-552 Kraków';

    /**
     * @var int|null Id of the instruction in a shop
     */
    private $id = null;

    /**
     * @var int|null Id of order which is connected with the instruction
     */
    private $orderId = null;

    /**
     * @var string Number of payment.
     *             It can be considered as a title of payment.
     *             Its format is like an operation number of Dotpay
     */
    private $number = '';

    /**
     * @var string Bank account number of Dotpay if the instruction applies to transfers payment
     */
    private $bankAccount = '';

    /**
     * @var int|null Id of channel which is used to make a payment
     */
    private $channel = null;

    /**
     * @var string Hash of payment which is used on Dotpay server
     */
    private $hash = '';

    /**
     * @var float Amount of money to pay
     */
    private $amount;

    /**
     * @var string Currency name
     */
    private $currency;

    /**
     * @var \DOMDocument/null Loaded DOM of site with payment instruction
     */
    private static $document = null;

    /**
     * Create the model based on data provided from shop.
     *
     * @param InstructionProviderInterface $provider Provider which contains data from shop application
     *
     * @return Instruction
     */
    public static function createFromData(InstructionProviderInterface $provider)
    {
        $instruction = new static();
        $instruction->setId($provider->getId())
                    ->setOrderId($provider->getOrderId())
                    ->setNumber($provider->getNumber())
                    ->setBankAccount($provider->getBankAccount())
                    ->setChannel($provider->getChannel())
                    ->setHash($provider->getHash())
                    ->setAmount($provider->getAmount())
                    ->setCurrency($provider->getCurrency());

        return $instruction;
    }

    /**
     * Return an id of the instruction in a shop.
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return id of order which is connected with the instruction.
     *
     * @return int|null
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Return a number of payment.
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Return a bank account number of Dotpay if the instruction applies to transfers payment.
     *
     * @return string|null
     */
    public function getBankAccount()
    {
        return $this->bankAccount;
    }

    /**
     * Return an id of channel which is used to make a payment.
     *
     * @return int|null
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * Return a hash of payment which is used on Dotpay server.
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Return an amount of payment.
     *
     * @return float
     */
    public function getAmount()
    {
        return AmountFormatter::format($this->amount, $this->getCurrency());
    }

    /**
     * Return a currency code of payment.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Return a flag which informs if the instruction applies to cash payment.
     *
     * @return bool
     */
    public function getIsCash()
    {
        return empty($this->bankAccount);
    }

    /**
     * Return url of pdf document with instruction of finishing payment.
     *
     * @param Configuration $config Configuration object
     *
     * @return string
     */
    public function getPdfUrl(Configuration $config)
    {
        return $config->getPaymentUrl().'instruction/pdf/'.$this->getNumber().'/'.$this->getHash().'/';
    }

    /**
     * Return a page of bank, where customer can make his transfer.
     *
     * @param Configuration $config Configuration object
     *
     * @return string|null
     */
    public function getBankPage(Configuration $config)
    {
        $document = $this->loadDOM($config);
        $channelContainer = $document->getElementById('channel_container_');
        if ($channelContainer instanceof \DOMElement) {
            return $channelContainer->getElementsByTagName('a')->item(0)->getAttribute('href');
        }

        return null;
    }

    /**
     * Return channel logo only for bank payment channels.
     *
     * @param Configuration $config Configuration object
     *
     * @return string/null
     */
    public function getBankChannelLogo(Configuration $config)
    {
        $document = $this->loadDOM($config);
        $channelImage = $config::DOTPAY_SSL_URL.$document->getElementById('channel_image_');
        if ($channelImage instanceof \DOMElement) {
            return $channelImage->getAttribute('src');
        }

        return null;
    }

    /**
     * Return url of the page with original payment instruction.
     *
     * @param Configuration $config Configuration object
     *
     * @return string
     */
    public function getPage(Configuration $config)
    {
        return $config->getPaymentUrl().'instruction/'.$this->getNumber().'/'.$this->getHash().'/';
    }

    /**
     * Set an id of the instruction in a shop.
     *
     * @param int $id Id of the instruction in a shop
     *
     * @return Instruction
     */
    public function setId($id)
    {
        $this->id = (int) $id;

        return $this;
    }

    /**
     * Set an Order id which is connected with the instruction.
     *
     * @param int $orderId Id of order which is connected with the instruction
     *
     * @return Instruction
     */
    public function setOrderId($orderId)
    {
        $this->orderId = (int) $orderId;

        return $this;
    }

    /**
     * Set a number of payment.
     *
     * @param string $number Number of payment
     *
     * @return Instruction
     *
     * @throws OperationNumberException Thrown when the given number is incorrect
     */
    public function setNumber($number)
    {
        if (!OpNumber::validate($number)) {
            throw new OperationNumberException($number);
        }
        $this->number = (string) $number;

        return $this;
    }

    /**
     * Set a bank account number of Dotpay if the instruction applies to transfers payment.
     *
     * @param string $bankAccount Bank account number of Dotpay if the instruction applies to transfers payment
     *
     * @return Instruction
     *
     * @throws BankNumberException Thrown when the given bank account number is incorrect
     */
    public function setBankAccount($bankAccount)
    {
        if (preg_match('/^\d{26}$/', trim($bankAccount)) === 1) {
            $bankAccount = 'PL'.$bankAccount;
        }
        if (!empty($bankAccount) && !BankNumber::validate($bankAccount)) {
            throw new BankNumberException($bankAccount);
        }
        $this->bankAccount = (string) $bankAccount;

        return $this;
    }

    /**
     * Set an id of channel which is used to make a payment.
     *
     * @param int $channel Id of channel which is used to make a payment
     *
     * @return Instruction
     *
     * @throws ChannelIdException Thrown when the given channel id is incorrect
     */
    public function setChannel($channel)
    {
        if (empty($channel) || !ChannelId::validate($channel)) {
            throw new ChannelIdException($channel);
        }
        $this->channel = (int) $channel;

        return $this;
    }

    /**
     * Set a hash of payment which is used on Dotpay server.
     *
     * @param string $hash Hash of payment which is used on Dotpay server
     *
     * @return Instruction
     */
    public function setHash($hash)
    {
        $this->hash = (string) $hash;

        return $this;
    }

    /**
     * Set an amount of the order.
     *
     * @param float $amount An amount of the order
     *
     * @return Order
     *
     * @throws AmountException Thrown when the given amount is incorrect
     */
    public function setAmount($amount)
    {
        if (!Amount::validate($amount)) {
            throw new AmountException($amount);
        }
        $this->amount = $amount;

        return $this;
    }

    /**
     * Set a currency code of the order.
     *
     * @param string $currency A currency code of the order
     *
     * @return Order
     *
     * @throws CurrencyException Thrown when the given currency is incorrect
     */
    public function setCurrency($currency)
    {
        $correctCurrency = strtoupper($currency);
        if (!Currency::validate($correctCurrency)) {
            throw new CurrencyException($correctCurrency);
        }
        $this->currency = (string) $correctCurrency;

        return $this;
    }

    /**
     * Return loaded DOM document with instruction of payment.
     *
     * @param Configuration $config Object with configuration
     *
     * @return \DOMDocument
     */
    protected function loadDOM(Configuration $config)
    {
        if (self::$document === null) {
            $pageUrl = $this->getPage($config);
            self::$document = new \DOMDocument();
            @self::$document->loadHTMLFile($pageUrl);
        }

        return self::$document;
    }
}
