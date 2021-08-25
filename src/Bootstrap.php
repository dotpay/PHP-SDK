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

namespace Dotpay;

/**
 * SDK class is not found.
 */
class DotpayClassNotFoundException extends \Exception
{
}

/**
 * SDK class is not in found file.
 */
class DotpayClassNotInFileException extends \Exception
{
}

/**
 * Basic functionality for initialization Dotpay SDK environment.
 */
class Bootstrap
{
    /**
     * Initialize SDK autoloading mechanism.
     */
    public static function initialize()
    {
        spl_autoload_register(__NAMESPACE__.'\\'.__CLASS__.'::dotpaySdkLoader');
    }

    /**
     * Return the directory of the main location of Dotpay SDK.
     *
     * @return string
     */
    public static function getMainDir()
    {
        return __DIR__;
    }

    /**
     * Return the directory of the main location of Dotpay SDK.
     *
     * @return string
     */
    public static function getLocaleDir()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'i18n';
    }

    /**
     * Load an SDK class.
     *
     * @param string $className Loader of SDK classes
     *
     * @return bool
     *
     * @throws DotpayClassNotFoundException  Thrown when Dotpay class isn't found in application
     * @throws DotpayClassNotInFileException Trown when Dotpay class isn't found in the found file
     */
    public static function dotpaySdkLoader($className)
    {
        if (strpos($className, 'Dotpay\\') !== 0) {
            return false;
        }
        $path = __DIR__.'/'.str_replace('\\', '/', $className).'.php';
        if (!file_exists($path)) {
            throw new DotpayClassNotFoundException($className);
        }
        include_once $path;
        if (!(class_exists($className) || interface_exists($className))) {
            throw new DotpayClassNotInFileException($className.' in: '.$path);
        }
    }
}
