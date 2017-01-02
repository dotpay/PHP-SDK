<?php

namespace Dotpay\Resource;

use \DateTime;
use Dotpay\Channel\Channel;
use Dotpay\Model\Redirect;
use Dotpay\Model\Operation;
use Dotpay\Model\Instruction;
use Dotpay\Model\Payer;
use Dotpay\Model\PaymentMethod;
use Dotpay\Resource\RegisterOrder\Result;
use Dotpay\Exception\Resource\PaymentNotCreatedException;
use Dotpay\Exception\Resource\InstructionNotFoundException;

class RegisterOrder extends Resource {
    const TARGET = "payment_api/v1/register_order/";
    
    public function create(Channel $channel) {
        $data2send = $this->getDataStructure($channel);
        $resultArray = $this->postData($this->config->getPaymentUrl().self::TARGET, $data2send);
        $info = $this->curl->getInfo();
        if ((int)$info['http_code'] !== 201) {
            throw new PaymentNotCreatedException();
        }
        $config = $this->config;
        $operation = new Operation($resultArray['operation']['type'], $resultArray['operation']['number']);
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
                        $resultArray['operation']['payer']['email'],
                        $resultArray['operation']['payer']['first_name'],
                        $resultArray['operation']['payer']['last_name']
                  ))
                  ->setPaymentMethod(new PaymentMethod(
                        $resultArray['operation']['payment_method']['channel_id']
                  ));
        
        $result = new Result(
            $resultArray['info']['status_url'],
            $operation
        );
        if(isset($resultArray['redirect'])) {
            $result->setRedirect(
                new Redirect(
                    $resultArray['redirect']['url'],
                    $resultArray['redirect']['data'],
                    $resultArray['redirect']['method'],
                    $resultArray['redirect']['encoding']
                )
            );
        }
        switch($channel->getGroup()) {
            case $config::transferGroup:
            case $config::cashGroup:
                return $this->processCashAndTransfer($resultArray, $result, $channel);
            default:
                return $result;
        }
    }
    
    private function processCashAndTransfer(array $resultArray, Result $resultObject, Channel $channel) {
        if(isset($resultArray['instruction'])) {
            $config = $this->config;
            $isCash = ($channel->getGroup() == $config::cashGroup);
            $instruction = new Instruction();
            $instruction->setAmount($resultArray['instruction']['amount'])
                        ->setCurrency($resultArray['instruction']['currency'])
                        ->setNumber($resultArray['instruction']['title'])
                        ->setChannel($resultArray['operation']['payment_method']['channel_id'])
                        ->setIsCash($isCash)
                        ->setHash($this->getHashFromResultArray($resultArray));
            if (!$isCash) {
                $instruction->setBankAccount($resultArray['instruction']['recipient']['bank_account_number']);
            }
            $resultObject->setInstruction($instruction);
        } else {
            throw new InstructionNotFoundException($resultArray['operation']['number']);
        }
        return $resultObject;
    }
    
    private function getHashFromResultArray(array $payment) {
        $parts = explode('/', $payment['instruction']['instruction_url']);
        return $parts[count($parts)-2];
    }


    private function getDataStructure(Channel $channel) {
        return array (
            'order' => array (
                'amount' => $channel->getTransaction()->getPayment()->getOrder()->getAmount(),
                'currency' => $channel->getTransaction()->getPayment()->getOrder()->getCurrency(),
                'description' => $channel->getTransaction()->getPayment()->getOrder()->getDescription(),
                'control' => $channel->getTransaction()->getPayment()->getOrder()->getId()
            ),

            'seller' => array (
                'account_id' => $channel->getTransaction()->getPayment()->getSeller()->getId(),
                'url' => $channel->getTransaction()->getBackUrl(),
                'urlc' => $channel->getTransaction()->getConfirmUrl(),
            ),

            'payer' => array (
                'first_name' => $channel->getTransaction()->getPayment()->getCustomer()->getFirstName(),
                'last_name' => $channel->getTransaction()->getPayment()->getCustomer()->getLastName(),
                'email' => $channel->getTransaction()->getPayment()->getCustomer()->getEmail(),
                'address' => array(
                    'street' => $channel->getTransaction()->getPayment()->getCustomer()->getStreet(),
                    'building_number' => $channel->getTransaction()->getPayment()->getCustomer()->getBuildingNumber(),
                    'postcode' => $channel->getTransaction()->getPayment()->getCustomer()->getPostCode(),
                    'city' => $channel->getTransaction()->getPayment()->getCustomer()->getCity(),
                    'country' => $channel->getTransaction()->getPayment()->getCustomer()->getCountry()
                )
            ),

            'payment_method' => array (
                'channel_id' => $channel->getChannelId()
            ),

            'request_context' => array (
                'ip' => $_SERVER['REMOTE_ADDR']
            )
        );
    }
}
