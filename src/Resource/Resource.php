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

use Dotpay\Model\Configuration;
use Dotpay\Tool\Curl;
use Dotpay\Exception\Resource\ServerException;
use Dotpay\Exception\Resource\ForbiddenException;
use Dotpay\Exception\Resource\UnauthorizedException;
use Dotpay\Exception\Resource\NotFoundException;
use Dotpay\Exception\Resource\UnavailableException;
use Dotpay\Exception\Resource\TimeoutException;

/**
 * Offer base functionality to use external Internet resources.
 */
abstract class Resource
{
    /**
     * @var Configuration Configuration of Dotpay payments
     */
    protected $config;

    /**
     * @var Curl Tool for using the cURL library
     */
    protected $curl;

    /**
     * @var array Information about last request
     */
    protected $info = null;

    /**
     * Initialize the resource.
     *
     * @param Configuration $config Configuration of Dotpay payments
     * @param Curl          $curl   Tool for using the cURL library
     */
    public function __construct(Configuration $config, Curl $curl)
    {
        $this->config = $config;
        $this->curl = $curl;
    }

    /**
     * Return an object with a tool for using the cURL library.
     *
     * @return Curl
     */
    public function getCurl()
    {
        return $this->curl;
    }

    /**
     * Close the resource.
     */
    public function close()
    {
        $this->curl->close();
        unset($this->curl);
    }

    /**
     * Return a parsed response from the external server.
     *
     * @param string $url Url of a destination request
     *
     * @return array Result which is decoded as array
     *
     * @throws UnauthorizedException Thrown when an authorization is failed
     * @throws ForbiddenException    Thrown when user doesn't have an access or when logging data is wrong
     * @throws NotFoundException     Thrown when a destination is not found
     * @throws UnavailableException  Thrown when the resource is not available
     * @throws TimeoutException      Thrown when timeout is exceed
     * @throws ServerException       Thrown when server of a destination does not work correctly
     */
    protected function getContent($url)
    {
        $this->curl->addOption(CURLOPT_SSL_VERIFYPEER, false)
                   ->addOption(CURLOPT_URL, $url);
        $headers = [
            $this->getAcceptHeader(),
            'Content-Type: application/json; charset=utf-8',
            'User-Agent: DotpaySDK',
        ];
        $this->curl->addOption(CURLOPT_HTTPHEADER, $headers);
        $this->curl->addOption(CURLOPT_RETURNTRANSFER, 1);
        $result = $this->curl->exec();
        $info = $this->curl->getInfo();
        $httpCode = (int) $info['http_code'];
        unset($info);
        if ($httpCode >= 200 && $httpCode < 300 || $httpCode == 400) {
            return json_decode($result, true);
        }
        switch ($httpCode) {
            case 401:
                throw new UnauthorizedException($url);
            case 403:
                throw new ForbiddenException($url);
            case 404:
                throw new NotFoundException($url);
            case 503:
                throw new UnavailableException($url);
            case 504:
                throw new TimeoutException($url);
            default:
                throw new ServerException($this->curl->error(), $httpCode);
        }
    }

    /**
     * Send a post data to the destination point and return a response.
     *
     * @param string $url  Url of a destination request
     * @param sring  $body Body content of request which is encoded as JSON string
     *
     * @return array
     */
    protected function postData($url, $body)
    {
        $this->curl->addOption(CURLOPT_POST, 1)
                   ->addOption(CURLOPT_POSTFIELDS, $body)
                   ->addOption(CURLOPT_USERPWD, $this->config->getUsername().':'.$this->config->getPassword());

        return $this->getContent($url, false);
    }

    /**
     * Send a delete request to the destination point and return a response.
     *
     * @param string $url  Url of a destination request
     *
     * @return array
     */
    protected function deleteData($url)
    {
        $this->curl
            ->addOption(CURLOPT_CUSTOMREQUEST, "DELETE")
            ->addOption(CURLOPT_USERPWD, $this->config->getUsername().':'.$this->config->getPassword());

        return $this->getContent($url, false);
    }

    /**
     * Return a string which contain a header with Accept rule.
     *
     * @return string
     */
    protected function getAcceptHeader()
    {
        return 'Accept: application/json; indent=4';
    }
}
