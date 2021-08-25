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

namespace Dotpay\Resource;

use DateTime;
use Dotpay\Loader\Loader;
use Dotpay\Channel\Channel;
use Dotpay\Model\Configuration;
use Dotpay\Model\Redirect;
use Dotpay\Model\Payer;
use Dotpay\Model\PaymentMethod;
use Dotpay\Tool\Curl;
use Dotpay\Tool\IpDetector;
use Dotpay\Resource\RegisterOrder\Result;
use Dotpay\Exception\Resource\PaymentNotCreatedException;
use Dotpay\Exception\Resource\InstructionNotFoundException;


/**
 * Provide an interface to use Register Method to create payments.
 */
class RegisterOrder extends Resource
{
    /**
     * Subaddress of the Retister API location.
     */
    const TARGET = 'payment_api/v1/register_order/';

    /**
     * @var Loader Instance of SDK Loader
     */
    private $loader;

    protected $MagentoUrl;

    /**
     * Initialize the resource.
     *
     * @param Configuration $config Configuration of Dotpay payments
     * @param Curl          $curl   Tool for using the cURL library
     */
    public function __construct(Configuration $config, Curl $curl)
    {
        parent::__construct($config, $curl);
        $this->loader = Loader::load();
    }

    /**
     * Create a new payment using Register order method.
     *
     * @param Channel $channel Data of channel which should be used to realizing the operation
     *
     * @return Result
     *
     * @throws PaymentNotCreatedException Thrown when payment is not created
     */
    public function create(Channel $channel)
    {
        $data2send = str_replace('\\/', '/', json_encode($this->getDataStructure($channel)));
        $resultArray = $this->postData($this->config->getPaymentUrl().self::TARGET, $data2send);
        $info = $this->curl->getInfo();
        if ((int) $info['http_code'] !== 201) {
            throw new PaymentNotCreatedException();
        }
        $operation = $this->loader->get('Operation', [$resultArray['operation']['type'], $resultArray['operation']['number']]);
        $operation->setUrl($resultArray['operation']['href'])
                ->setDateTime(new DateTime($resultArray['operation']['creation_datetime']))
                ->setStatus($resultArray['operation']['status'])
                ->setAmount($resultArray['operation']['amount'])
                ->setCurrency($resultArray['operation']['currency'])
                ->setOriginalAmount($resultArray['operation']['original_amount'])
                ->setOriginalCurrency($resultArray['operation']['original_currency'])
                ->setAccountId($resultArray['operation']['account_id'])
                ->setDescription($resultArray['operation']['description'])
                ->setPayer(new Payer(
                    $resultArray['operation']['payer']['email'], $resultArray['operation']['payer']['first_name'], $resultArray['operation']['payer']['last_name']
                ))
                ->setPaymentMethod(new PaymentMethod(
                    $resultArray['operation']['payment_method']['channel_id']
                )
        );
        $result = new Result(
                $resultArray['info']['status_url'], $operation
        );
        if (isset($resultArray['redirect'])) {
            $result->setRedirect(
                    new Redirect(
                    $resultArray['redirect']['url'], $resultArray['redirect']['data'], $resultArray['redirect']['method'], $resultArray['redirect']['encoding']
                    )
            );
        }
        switch ($channel->getGroup()) {
            case $channel::CASH_GROUP:
            case $channel::TRANSFER_GROUP:
                return $this->processCashAndTransfer($resultArray, $result, $channel);
            default:
                return $result;
        }
    }

    /**
     * Process cash and transfer payments.
     *
     * @param array   $resultArray  Informations which are given from Dotpay server after realizing the payment
     * @param Result  $resultObject Structure which contains selected result's informations
     * @param Channel $channel      Data of channel which should be used to realizing the operation
     *
     * @return Result
     *
     * @throws InstructionNotFoundException Thrown when an instruction of finishing payment is not found for cash and transfer payments
     */
    private function processCashAndTransfer(array $resultArray, Result $resultObject, Channel $channel)
    {
        if (isset($resultArray['instruction'])) {
            $isCash = ($channel->getGroup() == $channel::CASH_GROUP);
            $instruction = $this->loader->get('Instruction');
            $instruction->setOrderId($channel->getTransaction()->getPayment()->getId())
                    ->setNumber($resultArray['operation']['number'])
                    ->setTitle($resultArray['instruction']['title'])
                    ->setChannel($resultArray['operation']['payment_method']['channel_id'])
                    ->setHash($this->getHashFromResultArray($resultArray))
                    ->setAmount($resultObject->getOperation()->getAmount())
                    ->setCurrency($resultObject->getOperation()->getCurrency());
            if (!$isCash) {
                $instruction->setBankAccount($resultArray['instruction']['recipient']['bank_account_number']);
            }
            $resultObject->setInstruction($instruction);
        } else {
            throw new InstructionNotFoundException($resultArray['operation']['number']);
        }

        return $resultObject;
    }

    /**
     * Return a hash of payment based on payment's results.
     *
     * @param array $payment Details of payment
     *
     * @return string
     */
    private function getHashFromResultArray(array $payment)
    {
        $parts = explode('/', $payment['instruction']['instruction_url']);

        return (string) $parts[count($parts) - 2];
    }


    /**
     * Return the name of the sales domain
     * 
     * @return string
     */
    public function getHost($url) { 
        $parseUrl = parse_url(trim($url)); 
        if(isset($parseUrl['host']))
        {
            $host = $parseUrl['host'];
        }
        else
        {
             $path = explode('/', $parseUrl['path']);
             $host = $path[0];
        }
        return trim($host); 
     }

     /**
     * parsing the description to get the correct order number, 
     * then encoding the data to send them in the DpOrderId parameter - return address to the store: URL
     */
    public function getOrderIdtoUrl($desription,$control){
           
        preg_match("/[\d\/\d]+/", $desription, $matches_nr);
        
        
        if(isset($matches_nr[0])) {
            $matches2_nr = explode('/', $matches_nr[0]);
            
            if(isset($matches2_nr[0])) {
                $order_id = $matches2_nr[0];
            }else{
               $order_id  = null;
            } 
            if(isset($matches2_nr[1])) {
                $control_id = $matches2_nr[1];
            }else{
               $control_id  = $control ;
            }

        }else {
             $control_id  = $control;
             $order_id  = null;
        }

        // encode data to base64
        $idcontrol1 = base64_encode('#'.$control_id.'#'.$order_id.'#'.time()); 
        
        // simple trick to obstruct direct decoding this string from url:
        $idcontrol1  = str_replace('=','',$idcontrol1); 
        $rand = sha1(rand());
        $idcontrol2 = substr($idcontrol1, 0, 8).substr($rand, 13, 6).substr($idcontrol1, 8, strlen($idcontrol1));
        $idcontrol = "Ma:".substr($rand, 4, 3).$idcontrol2.substr(sha1(rand()), 10, 4).":RO";

        return $idcontrol;

}
    /**
     * Return a data structure for Register Order method.
     *
     * @param Channel $channel Data of channel which should be used to realizing the operation
     *
     * @return array
     */
    private function getDataStructure(Channel $channel)
    {
        
      $idcontrol_nr = $this->getOrderIdtoUrl($channel->getTransaction()->getPayment()->getDescription(),$channel->getTransaction()->getPayment()->getId());
      
      $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
      $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
      $MagentoUrl =  $this->getHost($storeManager->getStore()->getBaseUrl());
      
      $newControl = 'tr_id:#'.$channel->getTransaction()->getPayment()->getId().'|domain:'.$MagentoUrl.'|Magento DP module: v'.\Dotpay\Channel\Channel::DOTPAY_PLUGIN_VERSION.'|RO';

        $resultRO = [
            'order' => [
                'amount' => $channel->getTransaction()->getPayment()->getAmount(),
                'currency' => $channel->getTransaction()->getPayment()->getCurrency(),
                'description' => $channel->getTransaction()->getPayment()->getDescription(),
                //'control' => $channel->getTransaction()->getPayment()->getId()
                'control' => $newControl
            ],
            'seller' => [
                'account_id' => $channel->getTransaction()->getPayment()->getSeller()->getId(),
                'url' => $channel->getTransaction()->getBackUrl().'?DpOrderId='.$idcontrol_nr,
                'urlc' => $channel->getTransaction()->getConfirmUrl()
            ],
            'payer' => [
                'first_name' => $channel->getTransaction()->getCustomer()->getFirstName(),
                'last_name' => $channel->getTransaction()->getCustomer()->getLastName(),
                'email' => $channel->getTransaction()->getCustomer()->getEmail()
            ],
            'payment_method' => [
                'channel_id' => $channel->getChannelId()
            ],
            'request_context' => [
                'ip' => IpDetector::detect($this->config)
            ]
        ];
    
        if (!empty($channel->getTransaction()->getCustomer()->getBuildingNumber())) {
            $building_numberRO = $channel->getTransaction()->getCustomer()->getBuildingNumber();
        } else {
            $building_numberRO = '0'; //this field may not be blank in register order.
        }

        if ($this->isFilledAddress($channel)){
            $resultRO['payer']['address'] = [
                'street' => $channel->getTransaction()->getCustomer()->getStreet(),
                'building_number' => $building_numberRO,
                'postcode' => $channel->getTransaction()->getCustomer()->getPostCode(),
                'city' => $channel->getTransaction()->getCustomer()->getCity(),
                'country' => $channel->getTransaction()->getCustomer()->getCountry(),
            ];
        }

        return $resultRO;
	}

    /**
     * Check if address in transaction is correctly filled
     *
     * @param Channel $channel Data of channel which should be used to realizing the operation
     *
     * @return bool
     */
    private function isFilledAddress(Channel $channel)
    {
        $customer = $channel->getTransaction()->getCustomer();
        return !empty($customer->getStreet())
            && !empty($customer->getPostCode())
            && !empty($customer->getCity())
            && !empty($customer->getCountry());
    }
}