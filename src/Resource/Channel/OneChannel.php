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

namespace Dotpay\Resource\Channel;

/**
 * Represent a structure of informations about one payment channel.
 */
class OneChannel
{
    /**
     * @var array Informations about the one payment channel
     */
    private $data = [];

    /**
     * Initialize the object with the given data.
     *
     * @param array $data Informations about the one payment channel
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Return channel identifier.
     *
     * @return int
     */
    public function getId()
    {
        return (int) $this->get('id');
    }

    /**
     * Return a name of the channel.
     *
     * @return string
     */
    public function getName()
    {
        return (string) $this->get('name');
    }

    /**
     * Return the url where is located an image with a logo of the channel.
     *
     * @return string
     */
    public function getLogo()
    {
        return (string) $this->get('logo');
    }

    /**
     * Return a group of the channel.
     *
     * @return string
     */
    public function getGroup()
    {
        return (string) $this->get('group');
    }

    /**
     * Return a name of a channel's group.
     *
     * @return string
     */
    public function getGroupName()
    {
        return (string) $this->get('group_name');
    }

    /**
     * Return a short name of the channel.
     *
     * @return string
     */
    public function getShortName()
    {
        return (string) $this->get('short_name');
    }

    /**
     * Check if the channel is disabled.
     *
     * @return bool
     */
    public function isDisabled()
    {
        return $this->get('is_disable') !== 'False';
    }

    /**
     * Check if the channel is not online.
     *
     * @return bool
     */
    public function isNotOnline()
    {
        return $this->get('is_not_online') !== 'False';
    }

    /**
     * Return an array with list of fields which are needed on a payment form.
     *
     * @return array
     */
    public function getFormNames()
    {
        return $this->get('form_names');
    }

    /**
     * Return a value which is saved under the given key.
     *
     * @param string $name Key of a value
     *
     * @return mixed
     */
    protected function get($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        } else {
            return null;
        }
    }
}
