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

namespace Dotpay\Locale\Adapter;

use Dotpay\Exception\Locale\FileNotFoundException;

/**
 * Abstract adapter for recognizing files with translations.
 */
abstract class AbstractAdapter
{
    /**
     * @var string Directory where are located files with translation
     */
    private $dir;

    /**
     * @var string Name of used locale, for example pl_PL, en_US
     */
    private $locale;

    /**
     * @var string Extension of files recognized by the concrete instance of adapter
     */
    protected $extension = '';

    /**
     * @var string Name of default locale which is used if the given locale is not found
     */
    private static $defaultLocale = 'en_US';

    /**
     * @var bool Flag if content of the adapter is loaded
     */
    protected $loaded = false;

    /**
     * Initialize the adapter.
     *
     * @param string      $dir    Directory where are located files with translation
     * @param string|null $locale Name of used locale
     */
    public function __construct($dir, $locale = null)
    {
        $this->setDir($dir);
        $this->setLocale($locale);
    }

    /**
     * Return translated input string.
     *
     * @param string $sentence Input sentence to translate
     */
    abstract public function translate($sentence);

    /**
     * Read file and parse its content for using as a source of translations.
     *
     * @param string $filename Name of file with translations
     */
    abstract protected function readFile($filename);

    /**
     * Return an extension of recognized files with translations.
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Return a directory where are located files with translation.
     *
     * @return string
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * Set a directory where are located files with translation.
     *
     * @param string $dir Directory where are located files with translation
     *
     * @return AbstractAdapter
     */
    public function setDir($dir)
    {
        $this->dir = (string) $dir;

        return $this;
    }

    /**
     * Return a name of used locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set a name of used locale.
     *
     * @param string $locale Name of used locale
     *
     * @return AbstractAdapter
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Return a name of default locale.
     *
     * @return string
     */
    public function getDefaultLocale()
    {
        return self::$defaultLocale;
    }

    /**
     * Set a name of default locale.
     *
     * @param string $defaultLocale Name of default locale
     */
    public static function setDefaultLocale($defaultLocale)
    {
        self::$defaultLocale = (string) $defaultLocale;
    }

    /**
     * Load a file with translations.
     *
     * @param bool $defaultLocale A flag if the dafault locale is used
     *
     * @throws FileNotFoundException Thrown when a file with translations isn't found
     */
    protected function loadFile($defaultLocale = false)
    {
        $path = $this->getDir();
        if ($this->charAt($path, strlen($path)) !== DIRECTORY_SEPARATOR) {
            $path .= DIRECTORY_SEPARATOR;
        }
        $path .= $this->getLocale().'.'.$this->getExtension();
        if (file_exists($path)) {
            $this->readFile($path);
        } else {
            if ($defaultLocale == false) {
                $this->setLocale($this->getDefaultLocale());
                $this->loadFile(true);

                return;
            }
            throw new FileNotFoundException($path);
        }
        $this->loaded = true;
    }

    /**
     * Return a char on the the given position or null if the posion isn't exists in the string.
     *
     * @param string $str String where is searched the char
     * @param int    $pos Position of the searched char
     *
     * @return string|null
     */
    private function charAt($str, $pos)
    {
        if (strlen($str) <= $pos || $pos < 0) {
            return null;
        } else {
            return $str{(int) $pos};
        }
    }
}
