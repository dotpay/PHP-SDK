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

namespace Dotpay\Resource\Github;

use DateTime;
use Dotpay\Validator\Url;
use Dotpay\Exception\BadParameter\UrlException;

/**
 * Represent informations about the version of a software on Github server.
 */
class Version
{
    /**
     * @var string Number of the version
     */
    private $number;

    /**
     * @var string Url to Github API where is done request for the version
     */
    private $apiUrl;

    /**
     * @var string Url to Github where is published the version
     */
    private $url;

    /**
     * @var string Url address of a place where from it's possible do download a zip file with a software
     */
    private $zip;

    /**
     * @var DateTime Date and time when the version has been created
     */
    private $created;

    /**
     * @var DateTime Date and time when the version has been published
     */
    private $published;

    /**
     * Set basic informations about the version.
     *
     * @param string $number Number of the version
     * @param string $zip    Url address of a place where from it's possible do download a zip file with a software
     */
    public function __construct($number, $zip = null)
    {
        $this->setNumber($number);
        if($zip !== null) {
            $this->setZip($zip);
        }
    }

    /**
     * Return a number of the version.
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Return an url to Github API where is done request for the version.
     *
     * @return string
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * Return an url to Github where is published the version.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Return an url address of a place where from it's possible do download a zip file with a software.
     *
     * @return string
     */
    public function getZip()
    {
        if($this->zip === null) {
            return $this->getUrl();
        }
        return $this->zip;
    }

    /**
     * Return a date and time when the version has been created.
     *
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Return a date and time when the version has been published.
     *
     * @return DateTime
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Return a flag which is true if the given version number is older than number from the object.
     *
     * @param string $checkedNumber Number of checked version
     *
     * @return bool
     */
    public function isNewAvailable($checkedNumber)
    {
        return version_compare($checkedNumber, $this->getNumber(), '<');
    }

    /**
     * Set a number of the version.
     *
     * @param string $number Number of the version
     *
     * @return Version
     */
    public function setNumber($number)
    {
        $this->number = str_replace('v', '', $number);

        return $this;
    }

    /**
     * Set an url to Github API where is done request for the version.
     *
     * @param string $apiUrl Url to Github API where is done request for the version
     *
     * @return Version
     *
     * @throws UrlException
     */
    public function setApiUrl($apiUrl)
    {
        if (!Url::validate($apiUrl)) {
            throw new UrlException($apiUrl);
        }
        $this->apiUrl = $apiUrl;

        return $this;
    }

    /**
     * Set an url to Github where is published the version.
     *
     * @param string $url Url to Github where is published the version
     *
     * @return Version
     *
     * @throws UrlException
     */
    public function setUrl($url)
    {
        if (!Url::validate($url)) {
            throw new UrlException($url);
        }
        $this->url = $url;

        return $this;
    }

    /**
     * Set an url address of a place where from it's possible do download a zip file with a software.
     *
     * @param string $zip Url address of a place where from it's possible do download a zip file with a software
     *
     * @return Version
     *
     * @throws UrlException
     */
    public function setZip($zip)
    {
        if (!Url::validate($zip)) {
            throw new UrlException($zip);
        }
        $this->zip = $zip;

        return $this;
    }

    /**
     * Set a date and time when the version has been created.
     *
     * @param DateTime $created Date and time when the version has been created
     *
     * @return Version
     */
    public function setCreated(DateTime $created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Set a date and time when the version has been published.
     *
     * @param DateTime $published Date and time when the version has been published
     *
     * @return Version
     */
    public function setPublished(DateTime $published)
    {
        $this->published = $published;

        return $this;
    }
}
