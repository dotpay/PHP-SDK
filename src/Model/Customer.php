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

namespace Dotpay\Model;

use Dotpay\Provider\CustomerProviderInterface;
use Dotpay\Validator\Name;
use Dotpay\Validator\Street;
use Dotpay\Validator\BNumber;
use Dotpay\Validator\PostCode;
use Dotpay\Validator\Phone;
use Dotpay\Validator\Language;
use Dotpay\Exception\BadParameter\LanguageException;
use Dotpay\Exception\BadParameter\StreetException;
use Dotpay\Exception\BadParameter\BNumberException;
use Dotpay\Exception\BadParameter\PostCodeException;
use Dotpay\Exception\BadParameter\CityException;
use Dotpay\Exception\BadParameter\CountryException;
use Dotpay\Exception\BadParameter\PhoneException;

/**
 * Informations about a bank acount of payer.
 */
class Customer extends Payer
{
    /**
     * All available languages which are supported by Dotpay.
     */
    public static $LANGUAGES = array(
        'pl',
        'en',
        'de',
        'it',
        'fr',
        'es',
        'cz',
	'cs',
        'ru',
        'hu',
	'ro',
    );

    /**
     * @var int|null Id of the customer in a shop
     */
    private $id = null;

    /**
     * @var string Street name of the customer
     */
    private $street = '';

    /**
     * @var string Building number of the customer
     */
    private $buildingNumber = '';

    /**
     * @var string Post code of the customer
     */
    private $postCode = '';

    /**
     * @var string City of the customer
     */
    private $city = '';

    /**
     * @var string Country of the customer
     */
    private $country = '';

    /**
     * @var string Phone number of the customer
     */
    private $phone = '';

    /**
     * @var string Language used by the customer
     */
    private $language = '';

    /**
     * Create the model based on data provided from shop.
     *
     * @param CustomerProviderInterface $provider Provider which contains data from shop application
     *
     * @return Customer
     */
    public static function createFromData(CustomerProviderInterface $provider)
    {
        $customer = new static(
            $provider->getEmail(),
            $provider->getFirstName(),
            $provider->getLastName()
        );

        if($provider->isAddressAvailable()) {
            $customer->setStreet($provider->getStreet())
                     ->setBuildingNumber($provider->getBuildingNumber())
                     ->setPostCode($provider->getPostCode())
                     ->setCity($provider->getCity())
                     ->setCountry($provider->getCountry())
                     ->setPhone($provider->getPhone())
                     ->setLanguage($provider->getLanguage());
        }

        return $customer;
    }

    /**
     * Return an id of the customer in a shop.
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return a street name of the customer.
     *
     * @return string
     */
    public function getStreet()
    {
        $this->extractBnFromStreet();

        return $this->street;
    }

    /**
     * Return a building number of the customer.
     *
     * @return string
     */
    public function getBuildingNumber()
    {
        $this->extractBnFromStreet();

        return $this->buildingNumber;
    }

    /**
     * Return a post code of the customer.
     *
     * @return string
     */
    public function getPostCode()
    {
        return $this->postCode;
    }

    /**
     * Return a city of the customer.
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Return a country of the customer.
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Return a phone number of the customer.
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Return a language used by the customer.
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set an id of the customer in a shop.
     *
     * @param string $id Id of the customer in a shop
     *
     * @return Customer
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set a street name of the customer.
     *
     * @param string $street Street name of the customer
     *
     * @return Customer
     *
     * @throws StreetException Thrown when the given street is incorrect
     */
    public function setStreet($street)
    {
        if (!Street::validate($street)) {
            throw new StreetException($street);
        }
        $this->street = (string) $street;

        return $this;
    }

    /**
     * Set a building number of the customer.
     *
     * @param string $buildingNumber Building number of the customer
     *
     * @return Customer
     *
     * @throws BNumberException Thrown when the given building number is incorrect
     */
    public function setBuildingNumber($buildingNumber)
    {
        if (!BNumber::validate($buildingNumber)) {
            throw new BNumberException($buildingNumber);
        }
        $this->buildingNumber = (string) $buildingNumber;

        return $this;
    }

    /**
     * Set a post code of the customer.
     *
     * @param string $postCode Post code of the customer
     *
     * @return Customer
     *
     * @throws PostCodeException Thrown when the given post code is incorrect
     */
    public function setPostCode($postCode)
    {
        if (!PostCode::validate($postCode)) {
            throw new PostCodeException($postCode);
        }
        $this->postCode = (string) $postCode;

        return $this;
    }

    /**
     * Set a city of the customer.
     *
     * @param string $city City of the customer
     *
     * @return Customer
     *
     * @throws CityException Thrown when the given city is incorrect
     */
    public function setCity($city)
    {
        if (!Name::validate($city)) {
            throw new CityException($city);
        }
        $this->city = (string) $city;

        return $this;
    }

    /**
     * Set a country of the customer.
     *
     * @param string $country Country of the customer
     *
     * @return Customer
     *
     * @throws CountryException Thrown when the given country is incorrect
     */
    public function setCountry($country)
    {
        if (!Name::validate($country)) {
            throw new CountryException($country);
        }
        $this->country = (string) $country;

        return $this;
    }

    /**
     * Set a phone number of the customer.
     *
     * @param string $phone Phone number of the customer
     *
     * @return Customer
     *
     * @throws PhoneException Thrown when the given phone number is incorrect
     */
    public function setPhone($phone)
    {
        if (!Phone::validate($phone)) {
            throw new PhoneException($phone);
        }
        $this->phone = (string) $phone;

        return $this;
    }

    /**
     * Set a language used by the customer.
     *
     * @param string $language Language used by the customer
     *
     * @return Customer
     *
     * @throws LanguageException Thrown when the given language is incorrect
     */
    public function setLanguage($language)
    {
        if (!Language::validate($language)) {
            throw new LanguageException($language);
        }
        $this->language = (string) $language;

        return $this;
    }

    /**
     * Check if address details are available
     *
     * @return boolean
     */
    public function isAddressAvailable()
    {
        return $this->street !== ''
            && $this->postCode !== ''
            && $this->city !== ''
            && $this->country !== ''
            && $this->phone !== '';
    }

    /**
     * Try to extract a building number from the street name if it's an empty field.
     */
    private function extractBnFromStreet()
    {
        if (empty($this->buildingNumber) && !empty($this->street)) {
            preg_match("/\s[\w\d\/_\-]{0,30}$/", $this->street, $matches);
            if (count($matches) > 0) {
                $this->setBuildingNumber(trim($matches[0]));
                $this->setStreet(str_replace($matches[0], '', $this->street));
            }
        }
    }
}
