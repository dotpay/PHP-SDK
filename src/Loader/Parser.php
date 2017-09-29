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

namespace Dotpay\Loader;

use Dotpay\Bootstrap;
use Dotpay\Loader\Xml\Object;
use Dotpay\Loader\Xml\Param;
use Dotpay\Exception\Loader\XmlNotFoundException;
use SimpleXMLElement;

/**
 * Parser which parse and store XML files with a dependency structure.
 */
class Parser
{
    /**
     * @var SimpleXMLElement An object which represents XML preparsed file
     */
    private $xml;

    /**
     * @var array An array of Object elements which represent parsed object nodes
     */
    private $objects = [];

    /**
     * Initialize the parser object.
     *
     * @param string|null $fileName Name of an XML file with dependency structure. If null then the default di.xml will be used
     *
     * @throws XmlNotFoundException Thrown when XML file is not found
     */
    public function __construct($fileName = null)
    {
        if ($fileName === null) {
            $fileName = Bootstrap::getMainDir().DIRECTORY_SEPARATOR.'di.xml';
        }
        if (!file_exists($fileName)) {
            throw new XmlNotFoundException($fileName);
        }
        $this->xml = new SimpleXMLElement(file_get_contents($fileName));
    }

    /**
     * Return an array with Object elements created after parsing the XML file.
     *
     * @return array
     */
    public function getObjects()
    {
        if (empty($this->objects)) {
            $this->parse();
        }

        return $this->objects;
    }

    /**
     * Parse the XML file and build a list of Object elements.
     */
    private function parse()
    {
        foreach ($this->xml->object as $xmlObject) {
            $params = [];
            foreach ($xmlObject->param as $xmlParam) {
                $params[] = new Param($xmlParam['class'], $xmlParam['name'], $xmlParam['value']);
            }
            $this->objects[(string) $xmlObject['class']] = new Object($xmlObject['class'], $params, $xmlObject['alias'], $xmlObject['alwaysNew']);
        }
    }
}
