<?php

namespace Dotpay\Tool;

use \Exception;

/**
 * @codeCoverageIgnore
 */

/**
 * @author Esser Jan
 * @license MIT
 */
class IBAN
{
    protected $countryCode;
    protected $check;
    protected $bban;

    /**
     * assoc list of `country code => length` pairs that indicate how long tot total IBAN may be
     * @var array
     */
    protected static $IBANLengths = [
        'AL' => 28, 'AD' => 24, 'AT' => 20, 'AZ' => 28, 'BH' => 22, 'BE' => 16, 'BA' => 20, 'BR' => 29,
        'BG' => 22, 'CR' => 21, 'HR' => 21, 'CY' => 28, 'CZ' => 24, 'DK' => 18, 'DO' => 28, 'EE' => 20,
        'FO' => 18, 'FI' => 18, 'FR' => 27, 'GE' => 22, 'DE' => 22, 'GI' => 23, 'GR' => 27, 'GL' => 18,
        'GT' => 28, 'HU' => 28, 'IS' => 26, 'IE' => 22, 'IL' => 23, 'IT' => 27, 'JO' => 30, 'KZ' => 20,
        'KW' => 30, 'LV' => 21, 'LB' => 28, 'LI' => 21, 'LT' => 20, 'LU' => 20, 'MK' => 19, 'MT' => 31,
        'MR' => 27, 'MU' => 30, 'MC' => 27, 'MD' => 24, 'ME' => 22, 'NL' => 18, 'NO' => 15, 'PK' => 24,
        'PS' => 29, 'PL' => 28, 'PT' => 25, 'QA' => 29, 'RO' => 24, 'SM' => 27, 'SA' => 24, 'RS' => 22,
        'SK' => 24, 'SI' => 19, 'ES' => 24, 'SE' => 24, 'CH' => 21, 'TN' => 24, 'TR' => 26, 'AE' => 23,
        'GB' => 22, 'VG' => 24
    ];

    /**
     * International Back Account Number constructor.
     *
     * @param string $countryCode   country code using ISO 3166-1 alpha-2 - two letters
     * @param int    $check         check digits - two digits
     * @param string $bban          Basic Bank Account Number (BBAN) - up to 30 alphanumeric characters that
     *                              are country-specific. No special characters like `-` or space allowed here, use
     *                              the {@see createFromString} or {@see sanitize} instead if that's what you may have
     */
    public function __construct($countryCode, $check, $bban)
    {
        $this->countryCode = strtoupper($countryCode);
        $this->check       = $check;
        $this->bban        = strtoupper($bban);
        $this->validateSelf();
    }

    public static function createFromString($accountNumber)
    {
        $min = min(static::$IBANLengths) - 4; // first 4 are <country><check>
        $max = max(static::$IBANLengths) - 4; // first 4 are <country><check>
        if (!preg_match(
            '/^(:?IBAN)?([A-Za-z]{2})(\d{2})([A-Za-z0-9\s\-]{'. $min .','. $max . '})$/',
            static::sanitize($accountNumber),
            $matches
        )) {
            throw new Exception(
                'Invalid International Bank Account Number, not a valid format [' . $accountNumber . ']'
            );
        }
        return new static($matches[2], $matches[3], $matches[4]);
    }

    /**
     * removes common used white spacing and hyphenation from the account number
     *
     * @param string $accountNumber
     *
     * @return string
     */
    public static function sanitize($accountNumber)
    {
        return preg_replace('/[\s\-]*/', '', $accountNumber);
    }

    /**
     * Generating IBAN check digits
     * According to the ECBS "generation of the IBAN shall be the exclusive responsibility of the bank/branch servicing
     * the account".[8] The ECBS document replicates part of the ISO/IEC 7064:2003 standard as a method for generating
     * check digits in the range 02 to 98. Check digits in the ranges 00 to 96, 01 to 97, and 03 to 99 will also
     * provide validation of an IBAN, but the standard is silent as to whether or not these ranges may be used.
     *
     * The preferred algorithm is:
     *
     * 1. Check that the total IBAN length is correct as per the country. If not, the IBAN is invalid
     * 2. Replace the two check digits by 00 (e.g. GB00 for the UK)
     * 3. Move the four initial characters to the end of the string
     * 4. Replace the letters in the string with digits, expanding the string as necessary, such that A or a = 10,
     *    B or b = 11, and Z or z = 35. Each alphabetic character is therefore replaced by 2 digits
     * 5. Convert the string to an integer (i.e. ignore leading zeroes)
     * 6. Calculate mod-97 of the new number, which results in the remainder
     * 7. Subtract the remainder from 98, and use the result for the two check digits. If the result is a single digit
     *   number, pad it with a leading 0 to make a two-digit number
     */
    protected function validateSelf()
    {
        if (!isset(static::$IBANLengths[$this->countryCode])) {
            throw new Exception('Country for IBAN is not (yet) supported: ' . $this->countryCode);
        }
        // 1.
        if (strlen($this->countryCode . $this->check . $this->bban) !== static::$IBANLengths[$this->countryCode]) {
            throw new Exception('IBAN not long enough: ' . $this->__toString());
        }
        // 2. + 3. + 4. + 5. only checking upper case characters cause we strtoupper 'd it in constructor
        // and 0 cause then we can strip it in one go, we cant cast to int here due to 64bit limitation
        $checkString = preg_replace_callback(['/[A-Z]/', '/^[0]+/'], function ($matches) {
            if (substr($matches[0], 0, 1) !== '0') { // may be multiple leading 0's
                return base_convert($matches[0], 36, 10);
            }
            return '';
        }, $this->bban . $this->countryCode . '00');

        // 6. + 7.
        if (str_pad(98 - bcmod($checkString, 97), 2, '0', STR_PAD_LEFT) !== $this->check) {
            throw new Exception('IBAN is not valid: ' . $this->toFormattedString());
        }
    }

    public function __toString()
    {
        return $this->toFormattedString();
    }

    /**
     * @param string $separator  supported separators are white spaces (regex \s) and hyphen (-) all other separators
     *                           will not be able to be converted back into objects, a combination may be used.
     * @param int    $size       the separator group size to use, this will chunk
     * @param bool   $prefix     when true `IBAN ` will be prefixed
     *
     * @return string
     */
    public function toFormattedString($separator = ' ', $size = 4, $prefix = false)
    {
        $accountNumber = implode($separator, str_split($this->countryCode . $this->check . $this->bban, $size));
        if ($prefix) {
            $accountNumber = 'IBAN ' . $accountNumber;
        }
        return $accountNumber;
    }

    /**
     * a 'CountryCode' => length pair assoc array, country codes must be UPPER case
     * setting these will override the defaults
     *
     * @param array $IBANLengths
     */
    public static function setIBANLengths(array $IBANLengths)
    {
        static::$IBANLengths = $IBANLengths;
    }

    public function getIBANLengths()
    {
        return static::$IBANLengths;
    }
}
