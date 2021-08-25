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
        'uk',
        'sk',
        'lv',
        'lt'
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
                     ->setBuildingNumber($provider->getBuildingNumber(), $provider->getStreet())
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
        return $this->street;
    }

    /**
     * Return a building number of the customer.
     *
     * @return string
     */
    public function getBuildingNumber()
    {
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
        $street = $this->extractStreet($street);

        $Newstreet = trim(preg_replace('/[^\p{L}0-9\.\s\-\'_,]/u','',$street));
        if(strlen($Newstreet) > 100) {
            $Newstreet = substr($Newstreet,0,96)." _";
        }  


        if (!Street::validate($Newstreet)) {
            throw new StreetException($Newstreet);
        }
        $this->street = (string) $Newstreet;

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
    public function setBuildingNumber($buildingNumber, $street = null)
    {
        $buildingNumber = $this->extractBnFromStreet($buildingNumber, $street);

        $NewbuildingNumber = trim(preg_replace('/[^\p{L}0-9\s\-_\/]/u','',$buildingNumber));
        if(strlen($NewbuildingNumber) > 30){
            $NewbuildingNumber = substr($NewbuildingNumber,0,27)." _";
        }    


        if (!BNumber::validate($NewbuildingNumber)) {
            throw new BNumberException($NewbuildingNumber);
        }
        $this->buildingNumber = (string) $NewbuildingNumber;

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
        $NewpostCode =trim(preg_replace('/[^\d\w\s\-]/u','',$postCode));
        if(strlen($NewpostCode) > 20) {
            $NewpostCode = substr($NewpostCode,0,17)." _";
        } 

        if (!PostCode::validate($NewpostCode)) {
            throw new PostCodeException($postCode);
        }
        $this->postCode = (string) $NewpostCode;

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

        $Newcity =trim(preg_replace('/[^\p{L}0-9\.\s\-\'_,]/u','',$city));
        if(strlen($Newcity) > 50) {
            $Newcity = substr($Newcity,0,47)." _";
        } 

        if (!Name::validate($Newcity)) {
            throw new CityException($Newcity);
        }
        $this->city = (string) $Newcity;

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
            //throw new CountryException($country);
            $country = '';
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
        $NewPhone = preg_replace('/[^\+\s0-9\-_]/u','',$phone);
        if(strlen($NewPhone) > 20){
            $NewPhone = substr($NewPhone,0,17)." _";
        }    

        if (!Phone::validate($NewPhone)) {
           // throw new PhoneException($phone);
           $this->phone = null; //null
        }else{
            $this->phone = (string) $NewPhone;
        }
        
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
            //throw new LanguageException($language);
            $language = 'en';
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
    private function extractBnFromStreet($buildingNumber, $street = null)
    {
        if(is_null($street))
        $street = $this->street;
        if (empty($buildingNumber) && !empty($street)) {

            preg_match("/\s[\p{L}0-9\s\-_\/]{1,15}$/u", $street, $matches);
            if (count($matches) > 0) {

                $buildingNumber = preg_replace('/[^\p{L}0-9\s\-_\/]/u','',trim($matches[0]));

                $street2 = str_replace($matches[0], '', $street);
                $street = preg_replace('/[^\p{L}0-9\.\s\-\/_,]/u','',$street2);

            } else {
                $street = trim(preg_replace('/[^\p{L}0-9\.\s\-\/_,]/u','',$street));
            }
        } else {
            $street = trim(preg_replace('/[^\p{L}0-9\.\s\-\/_,]/u','',$street));
        }

        return $buildingNumber;
    }

    private function extractStreet($street)
    {
        $buildingNumber = $this->buildingNumber;

        if (empty($buildingNumber) && !empty($street)) 
        {

            preg_match("/\s[\p{L}0-9\s\-_\/]{1,15}$/u", $street, $matches);

            if (count($matches) > 0) {

                $buildingNumber = preg_replace('/[^\p{L}0-9\s\-_\/]/u','',trim($matches[0]));

                $street2 = str_replace($matches[0], '', $street);
                $street = preg_replace('/[^\p{L}0-9\.\s\-\/_,]/u','',$street2);

            } else {
                $street = trim(preg_replace('/[^\p{L}0-9\.\s\-\/_,]/u','',$street));
            }


        } else {

            $street = trim(preg_replace('/[^\p{L}0-9\.\s\-\/_,]/u','',$street));
        }


        $this->buildingNumber = $buildingNumber;
        return $street;
    }
}