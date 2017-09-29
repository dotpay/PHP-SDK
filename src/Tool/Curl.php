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

namespace Dotpay\Tool;

use Dotpay\Exception\ExtensionNotFoundException;

/**
 * Tool for a support of cURL library.
 */
class Curl
{
    /**
     * @var resource A cURL resource object
     */
    private $curl;

    /**
     * @var mixed informations returned by cURL after execution a command
     */
    private $info;

    /**
     * @var bool A flag which inform if the curl object is active
     */
    private $active = false;

    /**
     * Initialize the tool.
     *
     * @throws ExtensionNotFoundException Thrown when the cURL library is not installed as a PHP extension
     */
    public function __construct()
    {
        if ($this->checkExtension() == false) {
            throw new ExtensionNotFoundException('curl');
        }
        $this->curl = curl_init();
        if ($this->curl !== null) {
            $this->active = true;
        }
    }

    /**
     * Uninitialize the tool.
     */
    public function __destruct()
    {
        if ($this->active) {
            $this->close();
            $this->active = false;
        }
    }

    /**
     * Add a new cURL option to the configuration of the current cURL instance.
     *
     * @param int   $option A cURL option constant
     * @param mixed $value  A value which is set
     *
     * @return Curl
     */
    public function addOption($option, $value)
    {
        curl_setopt($this->curl, $option, $value);

        return $this;
    }

    /**
     * Perform a cURL session and returns a result.
     *
     * @return mixed
     */
    public function exec()
    {
        $response = curl_exec($this->curl);
        $this->info = curl_getinfo($this->curl);

        return $response;
    }

    /**
     * Return a string containing the last error for the current session.
     *
     * @return string
     */
    public function error()
    {
        return curl_error($this->curl);
    }

    /**
     * Return informations about the last operation.
     *
     * @return mixed
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Close the cURL session.
     *
     * @return Curl
     */
    public function close()
    {
        curl_close($this->curl);
        $this->curl = null;
        $this->active = false;

        return $this;
    }

    /**
     * Reset the cURL instance.
     *
     * @return Curl
     */
    public function reset()
    {
        curl_close($this->curl);
        $this->curl = curl_init();

        return $this;
    }

    /**
     * Check if the cURL extension for PHP is installed.
     *
     * @codeCoverageIgnore
     *
     * @return bool
     */
    protected function checkExtension()
    {
        return extension_loaded('curl');
    }
}
