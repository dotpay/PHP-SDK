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

namespace Dotpay\Model;

use Dotpay\Validator\Url;
use Dotpay\Exception\BadParameter\UrlException;
use Dotpay\Exception\BadParameter\MethodException;

/**
 * Informations about a redirect.
 */
class Redirect
{
    /**
     * List of allowed HTTP methods.
     */
    const ALLOWED_METHODS = [
        'get', 'post', 'put', 'delete',
    ];

    /**
     * @var string Target of the redirect
     */
    private $url = '';

    /**
     * @var array Data to send during the redirect.
     *            Keys of the array are names of values
     */
    private $data = [];

    /**
     * @var string Name of used HTTP method
     */
    private $method = 'post';

    /**
     * @var string Encoding type
     */
    private $encoding = 'utf-8';

    /**
     * Initialize the model.
     *
     * @param string $url      Target of the redirect
     * @param array  $data     Data to send during the redirect
     * @param string $method   Name of used HTTP method
     * @param string $encoding Encoding type
     */
    public function __construct($url, array $data, $method = 'post', $encoding = 'utf-8')
    {
        $this->setUrl($url);
        $this->setData($data);
        $this->setMethod($method);
        $this->setEncoding($encoding);
    }

    /**
     * Return a target of the redirect.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Return a data to send during the redirect.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Return a name of used HTTP method.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Return an encoding type.
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Set a target of the redirect.
     *
     * @param string $url Target of the redirect
     *
     * @return Redirect
     *
     * @throws UrlException Thrown when the given url is incorrect
     */
    public function setUrl($url)
    {
        if (!Url::validate($url)) {
            throw new UrlException($url);
        }
        $this->url = (string) $url;

        return $this;
    }

    /**
     * Set a data to send during the redirect.
     *
     * @param array $data Data to send during the redirect
     *
     * @return Redirect
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Set a name of used HTTP method.
     *
     * @param string $method Name of used HTTP method
     *
     * @return Redirect
     *
     * @throws MethodException
     */
    public function setMethod($method)
    {
        $method = strtolower($method);
        if (array_search($method, self::ALLOWED_METHODS) === false) {
            throw new MethodException($method);
        }
        $this->method = $method;

        return $this;
    }

    /**
     * Set an encoding type.
     *
     * @param string $encoding Encoding type
     *
     * @return Redirect
     */
    public function setEncoding($encoding)
    {
        $this->encoding = (string) $encoding;

        return $this;
    }
}
