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

use Dotpay\Validator\Id;
use Dotpay\Validator\Mcc;
use Dotpay\Validator\Url;
use Dotpay\Validator\Pin;
use Dotpay\Exception\BadParameter\IdException;
use Dotpay\Exception\BadParameter\MccException;
use Dotpay\Exception\BadParameter\UrlException;
use Dotpay\Exception\BadParameter\PinException;

/**
 * Informations about an account of a seller.
 */
class Account
{
    /**
     * @var int Seller id
     */
    private $id;

    /**
     * @var string|null Account status name
     */
    private $status = null;

    /**
     * @var string|null Name of a seller
     */
    private $name = null;

    /**
     * @var int|null MCC code
     */
    private $mcc = null;

    /**
     * @var string|null URL which is set as an URLc
     */
    private $urlc = null;

    /**
     * @var bool Flag which informs if external URLc is blocked
     */
    private $blockExternalUrlc = false;

    /**
     * @var string|null Pin for the seller id
     */
    private $pin = null;

    /**
     * Initialize the model.
     *
     * @param int $id Seller id
     */
    public function __construct($id)
    {
        $this->setId($id);
    }

    /**
     * Return a seller id.
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return an account status name.
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Return a name of the seller.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return an MCC code.
     *
     * @return int|null
     */
    public function getMcc()
    {
        return $this->mcc;
    }

    /**
     * Return an URLc.
     *
     * @return string|null
     */
    public function getUrlc()
    {
        return $this->urlc;
    }

    /**
     * Check if external URLc is blocked.
     *
     * @return bool
     */
    public function getBlockExternalUrlc()
    {
        return $this->blockExternalUrlc;
    }

    /**
     * Return a pin of the seller.
     *
     * @return string|null
     */
    public function getPin()
    {
        return $this->pin;
    }

    /**
     * Set the given seller id.
     *
     * @param int $id Seller id
     *
     * @return Account
     *
     * @throws IdException Thrown when the given seller id is incorrect
     */
    public function setId($id)
    {
        if (!Id::validate($id)) {
            throw new IdException($id);
        }
        $this->id = (int) $id;

        return $this;
    }

    /**
     * Set the given status name.
     *
     * @param string $status Account status name
     *
     * @return Account
     */
    public function setStatus($status)
    {
        $this->status = (string) $status;

        return $this;
    }

    /**
     * Set the given name of the seller.
     *
     * @param string $name Name of the seller
     *
     * @return Account
     */
    public function setName($name)
    {
        $this->name = (string) $name;

        return $this;
    }

    /**
     * Set the given MCC number.
     *
     * @param int $mcc MCC number
     *
     * @return Account
     *
     * @throws MccException Thrown when the given MCC number is incorrect
     */
    public function setMcc($mcc)
    {
        if (!empty($mcc) && !Mcc::validate($mcc)) {
            throw new MccException($mcc);
        }
        $this->mcc = (int) $mcc;

        return $this;
    }

    /**
     * Set the given URLc address.
     *
     * @param string $urlc URLc address
     *
     * @return Account
     *
     * @throws UrlException Thrown when the given URLc is incorrect
     */
    public function setUrlc($urlc)
    {
        if (!empty($urlc) && !Url::validate($urlc)) {
            throw new UrlException($urlc);
        }
        $this->urlc = (string) $urlc;

        return $this;
    }

    /**
     * Set a flag if external URLc is blocked or not.
     *
     * @param string $blockExternalUrlc A logical string ("true" or "false")
     *
     * @return Account
     */
    public function setBlockExternalUrlc($blockExternalUrlc)
    {
        $this->blockExternalUrlc = ($blockExternalUrlc == 'false');

        return $this;
    }

    /**
     * Set the given seller pin.
     *
     * @param string $pin Seller pin
     *
     * @return Account
     *
     * @throws PinException Thrown when the given seller pin is incorrect
     */
    public function setPin($pin)
    {
        if (!Pin::validate($pin)) {
            throw new PinException($pin);
        }
        $this->pin = (string) $pin;

        return $this;
    }
}
