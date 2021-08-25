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

namespace Dotpay\Resource\Channel;

/**
 * Represent a structure of single agreement data.
 */
class Agreement
{
    /**
     * @var array Data of the agreement
     */
    private $data = [];

    /**
     * Initialize the agreement structure.
     *
     * @param array $data Data of the agreement
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Return a type of the agreement.
     *
     * @return string
     */
    public function getType()
    {
        return (string) $this->get('type');
    }

    /**
     * Return a name of the agreement.
     *
     * @return string
     */
    public function getName()
    {
        return (string) $this->get('name');
    }

    /**
     * Return a label of the agreement.
     *
     * @return string
     */
    public function getLabel()
    {
        return (string) $this->get('label');
    }

    /**
     * Check if the agreement must be checked on a checkout page.
     *
     * @return bool
     */
    public function getRequired()
    {
        return (bool) $this->get('required');
    }

    /**
     * Check if the agreement is cheecked by default.
     *
     * @return bool
     */
    public function getDefault()
    {
        return (bool) $this->get('default');
    }

    /**
     * Return a descriotion text of the agreement.
     *
     * @return string
     */
    public function getDescription()
    {
        return (string) $this->get('description_text');
    }

    /**
     * Return a descriotion text decorated by HTML of the agreement.
     *
     * @return string
     */
    public function getDescriptionHtml()
    {
        return (string) $this->get('description_html');
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

    /**
     * Return all data belonged to the agreement.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Return string representation of the agreement.
     *
     * @return string
     */
    public function getJson()
    {
        return json_encode($this->data);
    }
}
