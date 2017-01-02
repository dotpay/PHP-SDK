<?php

namespace Dotpay\Resource;

use Dotpay\Model\Configuration;
use Dotpay\Tool\Curl;
use Dotpay\Model\Account;
use Dotpay\Model\BankAccount;
use Dotpay\Model\Operation;
use Dotpay\Model\Payer;
use Dotpay\Model\PaymentMethod;
use Dotpay\Model\CreditCard;
use Dotpay\Model\CardBrand;
use Dotpay\Model\Payout;
use Dotpay\Model\Seller as SellerModel;
use Dotpay\Validator\OpNumber;
use Dotpay\Exception\BadParameter\OperationNumberException;
use Dotpay\Exception\Resource\UnauthorizedException;
use Dotpay\Exception\Resource\ApiException;
use Dotpay\Exception\BadReturn\TypeNotCompatibleException;
use Dotpay\Exception\Resource\NotFoundException as ResourceNotFoundException;
use Dotpay\Exception\Resource\Account\NotFoundException as AccountNotFoundException;
use Dotpay\Exception\Resource\Operation\NotFoundException as OperationNotFoundException;

class Seller extends Resource {
    public function __construct(Configuration $config, Curl $curl) {
        parent::__construct($config, $curl);
        $this->curl->addOption(CURLOPT_USERPWD, $this->config->getUsername().':'.$this->config->getPassword());
    }
    
    public function isAccountRight() {
        try {
            $this->getDataFromApi('payments/');
        } catch (UnauthorizedException $e) {
            return false;
        }
        return true;
    }
    
    public function checkPin() {
        return $this->checkIdAndPin($this->config->getId(), $this->config->getPin());
    }
    
    public function checkFccPin() {
        return $this->checkIdAndPin($this->config->getFccId(), $this->config->getFccPin());
    }
    
    public function getAccount($id) {
        try {
            $response = $this->getDataFromApi('accounts/'.$id.'/?format=json');
        } catch (ResourceNotFoundException $ex) {
            throw new AccountNotFoundException($id);
        }
        $account = new Account($response['id']);
        $account->setStatus($response['status'])
                ->setName($response['name'])
                ->setMcc($response['mcc_code'])
                ->setUrlc($response['config']['urlc'])
                ->setBlockExternalUrlc($response['config']['block_external_urlc'])
                ->setPin($response['config']['pin']);
        return $account;
    }
    
    public function getOperationByNumber($number) {
        if(!OpNumber::validate($number))
            throw new OperationNumberException($number);
        try {
            $response = $this->getDataFromApi('operations/'.$number.'/?format=json');
            return $this->getWrapedOperation($response);
        } catch (ResourceNotFoundException $ex) {
            throw new OperationNotFoundException($number);
        }
    }
    
    public function getOperationById($id) {
        try {
            foreach($this->getPaginateDataFromApi('operations/?control='.$id.'&format=json') as $operation) {
                if($operation['control'] === $id)
                    return $this->getWrapedOperation($operation);
            }
        } catch (ResourceNotFoundException $ex) {
            throw new OperationNotFoundException($id);
        }
        throw new OperationNotFoundException($id);
    }
    
    public function makePayout(SellerModel $seller, Payout $payout) {
        $data = $this->getDataForPayout($seller, $payout);
        try {
            $this->postData('accounts/'.$seller->getId().'/payout/?format=json', json_encode($data));
            return true;
        } catch (ResourceNotFoundException $ex) {
            throw new AccountNotFoundException($seller->getId());
        }
    }
    
    private function getDataForPayout(SellerModel $seller, Payout $payout) {
        $data = [
            'currency' => $payout->getCurrency(),
            'transfers' => []
        ];
        foreach ($payout->getTransfers() as $transfer) {
            $data['transfers'][] = [
                'amount' => $transfer->getAmount(),
                'control' => $transfer->getControl(),
                'description' => $transfer->getDescription(),
                'recipient' => [
                    'name' => $transfer->getRecipient()->getName(),
                    'account_number' => $transfer->getRecipient()->getNumber()
                ],
            ];
        }
        return $data;
    }
    
    private function getWrapedOperation($input) {
        $operation = new Operation($input['type'], $input['number']);
            $operation->setUrl($input['href'])
                      ->setDateTime(new \DateTime($input['creation_datetime']))
                      ->setStatus($input['status'])
                      ->setAmount($input['amount'])
                      ->setCurrency($input['currency'])
                      ->setOriginalAmount($input['original_amount'])
                      ->setOriginalCurrency($input['original_currency'])
                      ->setAccountId($input['account_id'])
                      ->setDescription($input['description'])
                      ->setControl($input['control'])
                      ->setPayer(new Payer($input['payer']['email'], $input['payer']['first_name'], $input['payer']['last_name']));
            if($input['related_operation'] != null)
                $operation->setRelatedOperation($input['related_operation']);
            if(isset($input['payment_method'])) {
                if(isset($input['payment_method']['payer_bank_account'])) {
                    $bank = $input['payment_method']['payer_bank_account'];
                    $details = new BankAccount($bank['name'], $bank['number']);
                    $type = PaymentMethod::BANK_ACCOUNT;
                } else if(isset($input['payment_method']['credit_card'])) {
                    $cc = $input['payment_method']['credit_card'];
                    $details = new CreditCard(null, null);
                    $details->setBrand(new CardBrand($cc['brand']['name'], $cc['brand']['logo'], $cc['brand']['codename']))
                            ->setCardId($cc['id'])
                            ->setMask($cc['masked_number'])
                            ->setHref($cc['href']);
                    $type = PaymentMethod::CREDIT_CARD;
                }
                $operation->setPaymentMethod(new PaymentMethod($input['payment_method']['channel_id'], $details, $type));
            }
            return $operation;
    }
    
    private function checkIdAndPin($id, $pin) {
        $account = $this->getAccount($id);
        if($account->getId() === $id && $account->getPin() === $pin) {
            return true;
        }
        else {
            return false;
        }
    }
    
    private function getPaginateDataFromApi($fullUrl) {
        $this->curl->addOption(CURLOPT_USERPWD, $this->config->getUsername().':'.$this->config->getPassword());
        $content = $this->getContent($fullUrl);
        if(!is_array($content))
            throw new TypeNotCompatibleException(gettype($content));
        $info = $this->curl->getInfo();
        if(isset($info['http_code']) && $info['http_code'] == 400) {
            reset($content);
            throw new ApiException($content[key($content)]);
        }
        if($content['next'] === null) {
            return $content['results'];
        } else {
            return array_merge($content['results'], $this->getPaginateDataFromApi($content['next']));
        }
    }
    
    private function getDataFromApi($targetUrl) {
        $this->curl->addOption(CURLOPT_USERPWD, $this->config->getUsername().':'.$this->config->getPassword());
        $content = $this->getContent($this->getApiUrl($targetUrl));
        if(!is_array($content))
            throw new TypeNotCompatibleException(gettype($content));
        $info = $this->curl->getInfo();
        if(isset($info['http_code']) && $info['http_code'] == 400) {
            reset($content);
            throw new ApiException($content[key($content)]);
        }
        return $content;
    }
    
    private function getApiUrl($end) {
        return $this->config->getSellerUrl().'api/'.$end;
    }
}