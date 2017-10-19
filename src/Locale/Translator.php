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

namespace Dotpay\Locale;

/**
 * Tool for translating strings used in Dotpay SDK. It can use different adapters of source file.
 */
class Translator
{
    /**
     * @var Dotpay\Locale\Adapter\AbstractAdapter Adapter of source translations
     */
    private $adapter;

    /**
     * Initialize the translator.
     *
     * @param Dotpay\Locale\Adapter\AbstractAdapter $adapter Adapter of source translations
     */
    public function __construct(Adapter\AbstractAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Translate the given string replaced with.
     *
     * @param string $input  String to translation
     * @param array  $params Aray of params which should bee inserted to the result string
     *
     * @return string
     */
    public function __($input, array $params = [])
    {
        $translated = $this->adapter->translate($input);
        foreach ($params as $key => $value) {
            $translated = str_replace('%'.($key + 1), $value, $translated);
        }

        return $translated;
    }
}
