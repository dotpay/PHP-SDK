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

namespace Dotpay\Tool;

/**
 * Helper for formatting amount in currencies available in Dotpay.
 */
class AmountFormatter
{
    /**
     * List of all supported currencies.
     */
    public static $CURRENCY_PRECISION = [
        'EUR' => 2,
        'USD' => 2,
        'GBP' => 2,
        'JPY' => 0,
        'CZK' => 2,
        'SEK' => 2,
        'PLN' => 2,
    ];

    /**
     * Default precision of formatter.
     */
    const DEFAULT_PRECISION = 2;

    /**
     * Return a string with formated amount.
     *
     * @param float  $amount   Amount to format
     * @param string $currency Currency code
     * @param bool   $rounded  Flag if amount should be rounded by round() function
     *
     * @return string
     */
    public static function format($amount, $currency, $rounded = true)
    {
        if (isset(self::$CURRENCY_PRECISION[$currency])) {
            $precision = self::$CURRENCY_PRECISION[$currency];
        } else {
            $precision = self::DEFAULT_PRECISION;
        }

        if ($amount === null) {
            $amount = 0.0;
        } elseif ($rounded) {
            $amount = round($amount, $precision);
        }

        return number_format($amount, $precision);
    }
}
