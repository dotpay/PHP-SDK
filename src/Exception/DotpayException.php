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

namespace Dotpay\Exception;

use Dotpay\Bootstrap;
use Dotpay\Locale\Adapter\Csv;
use Dotpay\Locale\Translator;

/**
 * An error with Dotpay SDK occured.
 */
class DotpayException extends \RuntimeException
{
    /**
     * @var Dotpay\locale\Translator Translator for translating message of exceptions
     */
    private static $translator = null;

    /**
     * Message of error thrown by the exception.
     */
    const MESSAGE = 'An error with Dotpay services has been occured. Details: %1';

    /**
     * Initialize Dotpay SDK exception.
     *
     * @param string     $message  Details of exception
     * @param int        $code     Code of exception
     * @param \Exception $previous The previous exception used for the exception chaining
     */
    public function __construct($message = '', $code = 0, $previous = null)
    {
        if (self::$translator === null) {
            self::$translator = new Translator(new Csv(Bootstrap::getLocaleDir()));
        }
        parent::__construct(get_called_class().': '.self::$translator->__(static::MESSAGE, [$message]), $code, $previous);
    }
}
