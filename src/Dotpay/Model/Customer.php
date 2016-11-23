<?php

namespace Dotpay\Model;

use Dotpay\Validator\Name;
use Dotpay\Validator\Street;
use Dotpay\Validator\Email;
use Dotpay\Validator\BNumber;
use Dotpay\Validator\PostCode;
use Dotpay\Validator\Phone;
use Dotpay\Exception\BadParameter\LanguageException;
use Dotpay\Exception\BadParameter\FirstnameException;
use Dotpay\Exception\BadParameter\LastnameException;
use Dotpay\Exception\BadParameter\EmailException;
use Dotpay\Exception\BadParameter\StreetException;
use Dotpay\Exception\BadParameter\BNumberException;
use Dotpay\Exception\BadParameter\PostCodeException;
use Dotpay\Exception\BadParameter\CityException;
use Dotpay\Exception\BadParameter\CountryException;
use Dotpay\Exception\BadParameter\PhoneException;

class Customer {
    private $id;
    private $firstName;
    private $lastName;
    private $email;
    private $street;
    private $buildingNumber;
    private $postCode;
    private $city;
    private $country;
    private $phone;
    private $language;

    const LANGUAGES = array(
        'pl',
        'en',
        'de',
        'it',
        'fr',
        'es',
        'cz',
        'ru',
        'bg'
    );
    
    public function __construct($email, $firstName, $lastName) {
        $this->setEmail($email);
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
    }
    
    public function getId() {
        return $this->id;
    }

    public function getFirstName() {
        return $this->firstName;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getStreet() {
        $this->extractBnFromStreet();
        return $this->street;
    }

    public function getBuildingNumber() {
        $this->extractBnFromStreet();
        return $this->buildingNumber;
    }

    public function getPostCode() {
        return $this->postCode;
    }

    public function getCity() {
        return $this->city;
    }

    public function getCountry() {
        return $this->country;
    }

    public function getPhone() {
        return $this->phone;
    }
    
    public function getLanguage() {
        return $this->language;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setFirstName($firstName) {
        if(!Name::validate($firstName))
            throw new FirstnameException($firstName);
        $this->firstName = $firstName;
        return $this;
    }

    public function setLastName($lastName) {
        if(!Name::validate($lastName))
            throw new LastnameException($lastName);
        $this->lastName = $lastName;
        return $this;
    }

    public function setEmail($email) {
        if(!Email::validate($email))
            throw new EmailException($email);
        $this->email = $email;
        return $this;
    }

    public function setStreet($street) {
        if(!Street::validate($street))
            throw new StreetException($street);
        $this->street = $street;
        return $this;
    }

    public function setBuildingNumber($buildingNumber) {
        if(!BNumber::validate($buildingNumber))
            throw new BNumberException($buildingNumber);
        $this->buildingNumber = $buildingNumber;
        return $this;
    }

    public function setPostCode($postCode) {
        if(!PostCode::validate($postCode))
            throw new PostCodeException($postCode);
        $this->postCode = $postCode;
        return $this;
    }

    public function setCity($city) {
        if(!Name::validate($city))
            throw new CityException($city);
        $this->city = $city;
        return $this;
    }

    public function setCountry($country) {
        if(!Name::validate($country))
            throw new CountryException($country);
        $this->country = $country;
        return $this;
    }
    
    public function setPhone($phone) {
        if(!Phone::validate($phone))
            throw new PhoneException($phone);
        $this->phone = $phone;
        return $this;
    }

    public function setLanguage($language) {
        if(!in_array($language, self::LANGUAGES))
            throw new LanguageException($language);
        $this->language = $language;
        return $this;
    }
    
    private function extractBnFromStreet() {
        if(empty($this->buildingNumber) && !empty($this->street)) {
            preg_match("/\s[\w\d\/_\-]{0,30}$/", $this->street, $matches);
            if(count($matches)>0) {
                $this->setBuildingNumber(trim($matches[0]));
                $this->setStreet(str_replace($matches[0], '', $this->street));
            }
        }
    }
}

?>
