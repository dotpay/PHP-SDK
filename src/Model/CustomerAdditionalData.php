<?php
/**
 * Copyright (c) 2018 Dotpay sp. z o.o. <tech@dotpay.pl>.
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
 * @copyright Dotpay sp. z o.o.
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */

namespace Dotpay\Model;

use Dotpay\Provider\CustomerProviderInterface;

/**
 * Informations about a bank acount of payer.
 */
class CustomerAdditionalData extends Customer implements \JsonSerializable
{

    /**
     * @var \DateTime Day of user's registration
     */
    private $registeredSince;

    /**
     * @var int Number of user's orders
     */
    private $orderCount = 0;

    /**
     * @var string Type of delivery
     */
    private $deliveryType;

    /**
     * Create the model based on data provided from shop.
     *
     * @param CustomerProviderInterface $provider Provider which contains data from shop application
     *
     * @return CustomerAdditionalData
     */
    public static function createFromData(CustomerProviderInterface $provider)
    {
        $customer = new static(
            $provider->getEmail(),
            $provider->getFirstName(),
            $provider->getLastName()
        );

        if($provider->isAddressAvailable()) {
            $customer->setStreet($provider->getShippingStreet())
                ->setBuildingNumber($provider->getShippingBuildingNumber(), $provider->getShippingStreet())
                ->setPostCode($provider->getShippingPostCode())
                ->setCity($provider->getShippingCity())
                ->setCountry($provider->getShippingCountry())
                ->setPhone($provider->getPhone());
        }
        
        if($provider)

        return $customer;
    }

    /**
     * @return \DateTime
     */
    public function getRegisteredSince()
    {
        return $this->registeredSince;
    }

    /**
     * @param \DateTime $registeredSince
     * @return CustomerAdditionalData
     */
    public function setRegisteredSince($registeredSince)
    {
        $this->registeredSince = $registeredSince;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrderCount()
    {
        return $this->orderCount;
    }

    /**
     * @param int $orderCount
     * @return CustomerAdditionalData
     */
    public function setOrderCount($orderCount)
    {
        $this->orderCount = $orderCount;
        return $this;
    }

    /**
     * @return string
     */
    public function getDeliveryType()
    {
        return $this->deliveryType;
    }

    /**
     * @param string $deliveryType
     * @return CustomerAdditionalData
     */
    public function setDeliveryType($deliveryType)
    {
        $this->deliveryType = $deliveryType;
        return $this;
    }



    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        $data = [
            'payer' => [
                'first_name' => $this->getFirstName(),
                'last_name'  => $this->getLastName(),
                'email'      => $this->getEmail(),
                'phone'      => $this->getPhone()
            ],
            'order' => [
                'delivery_address' => [
                    'city'              => $this->getCity(),
                    'street'            => $this->getStreet(),
                    'building_number'   => $this->getBuildingNumber(),
                    'postcode'          => $this->getPostCode(),
                    'country'           => $this->getCountry()
                ]
            ]
        ];

        if($this->getRegisteredSince())
        {
            $data['registered_since'] = $this->getRegisteredSince()->format("Y-m-d");
            $data['order_count'] = $this->getOrderCount();
        }
        if($this->getDeliveryType())
        {
            $data['order']['delivery_type'] = $this->getDeliveryType();
        }

        return $data;
    }

    public function __toString()
    {
        return base64_encode(json_encode($this, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
