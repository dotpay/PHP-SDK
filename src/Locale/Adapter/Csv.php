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

namespace Dotpay\Locale\Adapter;

use Dotpay\Exception\Locale\TranslationNotFoundException;
use Dotpay\Exception\Locale\FileNotFoundException;

/**
 * Adapter which recognizes csv files with translations.
 */
class Csv extends AbstractAdapter
{
    /**
     * @var array Saved translations
     */
    private $translations = [];

    /**
     * Initialize the CSV adapter.
     *
     * @param string      $dir    Directory where are located files with translation
     * @param string|null $locale Name of used locale
     */
    public function __construct($dir, $locale = null)
    {
        $this->extension = 'csv';
        parent::__construct($dir, $locale);
    }

    /**
     * Return given translated.
     *
     * @param type $sentence
     *
     * @return type
     *
     * @throws TranslationNotFoundException
     */
    public function translate($sentence)
    {
        try {
            if (!$this->loaded) {
                $this->loadFile();
            }
            $hash = $this->getHash($sentence);
            if (isset($this->translations[$hash])) {
                return $this->translations[$hash];
            } else {
                throw new TranslationNotFoundException($sentence);
            }
        } catch (FileNotFoundException $e) {
            return $sentence;
        }
    }

    /**
     * Read a file from the given directory.
     *
     * @param string $filename
     */
    protected function readFile($filename)
    {
        $handle = fopen($filename, 'r');
        while (($data = fgetcsv($handle, 0, '=')) !== false) {
            $this->translations[$this->getHash($data[0])] = trim($data[1]);
        }
        fclose($handle);
    }

    /**
     * Return hash string of input.
     *
     * @param string $input
     *
     * @return string
     */
    private function getHash($input)
    {
        return sha1(trim($input));
    }
}
