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

namespace Dotpay\Channel;

use Dotpay\Loader\Loader;
use Dotpay\Validator\ChannelId;
use Dotpay\Resource\Payment as PaymentResource;
use Dotpay\Resource\Seller as SellerResource;
use Dotpay\Model\Seller as SellerModel;
use Dotpay\Model\Payment;
use Dotpay\Model\PaymentLink;
use Dotpay\Model\Transaction;
use Dotpay\Model\Configuration;
use Dotpay\Exception\Channel\SellerNotGivenException;
use Dotpay\Exception\BadParameter\ChannelIdException;
use Dotpay\Exception\Resource\Channel\NotFoundException;
use Dotpay\Exception\BadParameter\IncompatibleTypeException;
use Dotpay\Resource\Channel\Agreement;
use Dotpay\Html\Form\Input;
use Dotpay\Html\Container\Script;
use Dotpay\Html\PlainText;
use Dotpay\Html\Container\Form;



/**
 * Class provides a special functionality for customization of payments channel.
 */
class Channel
{
    /**
     * Name of cash channels group.
     */
    const CASH_GROUP = 'cash';

    /**
     * Name of transfer channels group.
     */
    const TRANSFER_GROUP = 'transfers';

    /**
     * Last version number of the plugin
     */
    const DOTPAY_PLUGIN_VERSION = '1.0.18';

    /**
     * @var int Code number of payment channel in Dotpay system
     */
    protected $code;

    /**
     * @var array Array of values which can be set for saving some additional informations
     */
    protected $reqistry = [];

    /**
     * @var Configuration Dotpay configuration object
     */
    protected $config;


    /**
     * @var \Dotpay\Resource\Channel\ChannelInfo A channel info struct, downloaded from Dotpay server
     */
    protected $channelInfo;

    /**
     * @var array An agreement struct, downloaded from Dotpay server
     */
    protected $agreements = [];

    /**
     * @var bool Flag of an availability of the channel
     */
    protected $available = false;

    /**
     * @var Transaction Object with transaction details
     */
    protected $transaction;

    /**
     * @var PaymentResource Payment resource which can be used for Payment API
     */
    protected $paymentResource;

    /**
     * @var SellerResource Seller resource which can be used for Payment API
     */
    protected $sellerResource;

    /**
     * @var string Title which can be displayed on the channel list
     */
    protected $title = '';

    /**
     * @var Seller Object of used seller
     */
    protected $seller = null;

    /**
     * @var \Magento\Checkout\Model\Session Session of checkout
     */
    protected $_checkoutSession;

    /**
     * Initialize a separate channel.
     *
     * @param int             $channelId       Code number of payment channel in Dotpay system
     * @param string          $code            Short string code which can be used to identify
     * @param Configuration   $config          Dotpay configuration object
     * @param Transaction     $transaction     Object with transaction details
     * @param PaymentResource $paymentResource Payment resource which can be used for Payment API
     * @param \Magento\Checkout\Model\Session                  $checkoutSession
     * @param SellerResource  $sellerResource  Seller resource which can be used for Seller API
     */
    public function __construct(
        $channelId, 
        $code, 
        Configuration $config, 
        Transaction $transaction, 
        PaymentResource $paymentResource, 
        SellerResource $sellerResource
       )
    {
        $this->code = $code;
        $this->config = $config;
        $this->transaction = $transaction;
        $this->paymentResource = $paymentResource;
        $this->sellerResource = $sellerResource;
        if (!$this->isVisible()) {
            return;
        }
        $this->chooseSeller();
        $this->transaction->getPayment()->setSeller($this->seller);
        $this->setChannelInfo($channelId);

    }

    /**
     * Save the given value for the name.
     *
     * @param string $name  The name of the value
     * @param mixed  $value The value to saving
     *
     * @return Channel
     */
    public function set($name, $value)
    {
        $this->reqistry[$name] = $value;

        return $this;
    }

    /**
     * Get the saved value by the given name.
     *
     * @param string $name Name of the saved value
     *
     * @return mixed
     */
    public function get($name)
    {
        if (isset($this->reqistry[$name])) {
            return $this->reqistry[$name];
        } else {
            return null;
        }
    }



    /**
     * Return a code number of payment channel in Dotpay system.
     *
     * @return int|null
     */
    public function getChannelId()
    {
        if ($this->channelInfo !== null) {
            return $this->channelInfo->getId();
        } else {
            return null;
        }
    }

    /**
     * Return a readable name of the channel.
     *
     * @return string|null
     */
    public function getName()
    {
        if ($this->channelInfo !== null) {
            return $this->channelInfo->getName();
        } else {
            return null;
        }
    }

    /**
     * Return a name of a group to which it belongs the channel.
     *
     * @return string|null
     */
    public function getGroup()
    {
        if ($this->channelInfo !== null) {
            return $this->channelInfo->getGroup();
        } else {
            return null;
        }
    }

    /**
     * Return an URL of a image with logo of the payment channel.
     *
     * @return string|null
     */
    public function getLogo()
    {
        if ($this->channelInfo !== null) {
            return $this->channelInfo->getLogo();
        } else {
            return null;
        }
    }

    /**
     * Return a short string code of the payment channel.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Return a title which can be displayed on the channel list.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the given seller.
     *
     * @param SellerModel $seller Model of shop seller
     *
     * @return Channel
     */
    public function setSeller(SellerModel $seller)
    {
        $this->seller = $seller;

        return $this;
    }

    /**
     * Check a visibility of the channel on a channels list.
     *
     * @return bool
     */
    public function isVisible()
    {
        return $this->isEnabled() &&
               $this->config->isGatewayEnabled(
                    $this->transaction->getPayment()->getCurrency()
               );
    }

    /**
     * Check an availability of the channel.
     *
     * @return bool
     */
    final public function isAvailable()
    {
        return $this->available && $this->isEnabled();
    }

    /**
     * Check if the channel is enabled to using.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->config->getEnable();
    }

    /**
     * Return an array of fields which can be displayed on a list of payment channels.
     * They can contain aditional fields with information which are needed before continue a payment process.
     *
     * @return array
     */
    public function getViewFields()
    {
        $data = array();

        return $data;
    }

    /**
     * Return view fields enriched by an additional piece of HTML code.
     *
     * @return array
     */
    public function getViewFieldsHtml()
    {
        return $this->getViewFields();
    }

    /**
     * Parsing domain from a URL
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
        $idcontrol = "Ma:".substr($rand, 4, 3).$idcontrol2.substr(sha1(rand()), 10, 4).":IN";

        return $idcontrol;

}


    /**
     * Return array of hidden fields for a form to redirecting to a Dotpay site with all needed information about a current payment.
     *
     * @return array
     *
     * @throws SellerNotGivenException Thrown when seller object is not given to payment channel
     */
    protected function prepareHiddenFields()
    {
        if ($this->seller === null) {
            throw new SellerNotGivenException();
        }
        
        $idcontrol_nr = $this->getOrderIdtoUrl($this->transaction->getPayment()->getDescription(),$this->transaction->getPayment()->getId());

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
            $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
            $this->MagentoUrl =  $this->getHost($storeManager->getStore()->getBaseUrl());

        $newControl = 'tr_id:#'.$this->transaction->getPayment()->getId().'|domain:'.$this->MagentoUrl.'|Magento DP module: v'.self::DOTPAY_PLUGIN_VERSION;
        $data = [];
        $data['id'] = $this->seller->getId();
        if($this->config->getControlDefault()){
            $data['control'] = $this->transaction->getPayment()->getId();
        }else{
            $data['control'] = $newControl;
        }
        $sellerInfo = $this->config->getShopName();
        if (!empty($sellerInfo)) {
            $data['p_info'] = $sellerInfo;
        }
        $sellerEmail = $this->config->getShopEmail();
        if (!empty($sellerEmail)) {
            $data['p_email'] = $sellerEmail;
        }
        $data['amount'] = $this->transaction->getPayment()->getAmount();
        $data['currency'] = $this->transaction->getPayment()->getCurrency();
        $data['description'] = $this->transaction->getPayment()->getDescription();
        $data['lang'] = $this->transaction->getCustomer()->getLanguage();
        $data['url'] = $this->transaction->getBackUrl().'?DpOrderId='.$idcontrol_nr;
        $data['urlc'] = $this->transaction->getConfirmUrl();
        $data['api_version'] = $this->config->getApi();
        $data['type'] = 4;
        $data['ch_lock'] = 0;
        $data['firstname'] = $this->transaction->getCustomer()->getFirstName();
        $data['lastname'] = $this->transaction->getCustomer()->getLastName();
        $data['email'] = $this->transaction->getCustomer()->getEmail();
        if($this->transaction->getCustomer()->isAddressAvailable()) {
            $data['phone'] = $this->transaction->getCustomer()->getPhone();
            $data['street'] = $this->transaction->getCustomer()->getStreet();
            $data['street_n1'] = $this->transaction->getCustomer()->getBuildingNumber();
            $data['city'] = $this->transaction->getCustomer()->getCity();
            $data['postcode'] = $this->transaction->getCustomer()->getPostCode();
            $data['country'] = $this->transaction->getCustomer()->getCountry();
        }
        $data['bylaw'] = 1;
        $data['personal_data'] = 1;
        $data['channel'] = $this->getChannelId();
        $data['customer'] = (string) $this->transaction->getCustomerAdditionalData();
        $data['ignore_last_payment_channel'] = 1;
        
        return $data;
    }

    /**
     * Return an array with all hidden fields including CHK.
     *  
     * @return array
     */
    public function getAllHiddenFields()
    {
        $data = $this->prepareHiddenFields();
        $data['chk'] = self::getCHK($data, $this->seller->getPin(), $this->transaction->getSubPayments());

        return $data;
    }

    /**
     * Return a form with all hidden fields for payment.
     *
     * @return Form
     */
    public function getHiddenForm()
    {
        $fields = [];
        foreach ($this->getAllHiddenFields() as $name => $value) {
            $fields[] = new Input('hidden', $name, (string) $value);
        }
        $fields[] = new Script(new PlainText('setTimeout(function(){document.getElementsByClassName(\'dotpay-form\')[0].submit();}, 10);'));
        $form = new Form($fields);

        return $form->setClass('dotpay-form')
                    ->setMethod('post')
                    ->setAction($this->config->getPaymentUrl());
    }

    /**
     * Return an array with agreement structs, downloaded from Dotpay server.
     *
     * @return array
     */
    public function getAgreements()
    {
        return $this->agreements;
    }

    /**
     * Return object with transaction details.
     *
     * @return Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * Return a configuration object.
     *
     * @return Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Check if the channel can have an instruction.
     *
     * @return bool
     */
    public function canHaveInstruction()
    {
        return $this->config->getInstructionVisible() &&
               $this->sellerResource->isAccountRight() &&
               ($this->getGroup() == self::CASH_GROUP ||
               $this->getGroup() == self::TRANSFER_GROUP);
    }

    /**
     * Add a new Agreement object for channel.
     *
     * @param Agreement $agreement The agreement to add to channel
     *
     * @return Channel
     */
    public function addAgreement(Agreement $agreement)
    {
        $this->agreements[] = $agreement;

        return $this;
    }

    /**
     * Set a title which can be displayed on the channel list.
     *
     * @param string $title Title which can be displayed on the channel list
     *
     * @return Channel
     */
    public function setTitle($title)
    {
        $this->title = (string) $title;

        return $this;
    }

    /**
     * Return saved Seller model.
     *
     * @return SellerModel
     */
    public function getSeller()
    {
        return $this->seller;
    }

    /**
     * Calculate CHK for the given data.
     *
     * @param array  $inputParameters Array with transaction parameters
     * @param string $pin             Seller pin to sign the control sum
     * @param array Array of subpayments
     *
     * @return string
     */

    public static function getCHK($inputParameters, $pin, array $subPayments = [])
    {
        $CHkInputString =
            $pin.
            (isset($inputParameters['api_version']) ? $inputParameters['api_version'] : null).
            (isset($inputParameters['charset']) ? $inputParameters['charset'] : null).
            (isset($inputParameters['lang']) ? $inputParameters['lang'] : null).
            (isset($inputParameters['id']) ? $inputParameters['id'] : null).
            (isset($inputParameters['amount']) ? $inputParameters['amount'] : null).
            (isset($inputParameters['currency']) ? $inputParameters['currency'] : null).
            (isset($inputParameters['description']) ? $inputParameters['description'] : null).
            (isset($inputParameters['control']) ? $inputParameters['control'] : null).
            (isset($inputParameters['channel']) ? $inputParameters['channel'] : null).
            (isset($inputParameters['credit_card_brand']) ? $inputParameters['credit_card_brand'] : null).
            (isset($inputParameters['ch_lock']) ? $inputParameters['ch_lock'] : null).
            (isset($inputParameters['channel_groups']) ? $inputParameters['channel_groups'] : null).
            (isset($inputParameters['onlinetransfer']) ? $inputParameters['onlinetransfer'] : null).
            (isset($inputParameters['url']) ? $inputParameters['url'] : null).
            (isset($inputParameters['type']) ? $inputParameters['type'] : null).
            (isset($inputParameters['buttontext']) ? $inputParameters['buttontext'] : null).
            (isset($inputParameters['urlc']) ? $inputParameters['urlc'] : null).
            (isset($inputParameters['firstname']) ? $inputParameters['firstname'] : null).
            (isset($inputParameters['lastname']) ? $inputParameters['lastname'] : null).
            (isset($inputParameters['email']) ? $inputParameters['email'] : null).
            (isset($inputParameters['street']) ? $inputParameters['street'] : null).
            (isset($inputParameters['street_n1']) ? $inputParameters['street_n1'] : null).
            (isset($inputParameters['street_n2']) ? $inputParameters['street_n2'] : null).
            (isset($inputParameters['state']) ? $inputParameters['state'] : null).
            (isset($inputParameters['addr3']) ? $inputParameters['addr3'] : null).
            (isset($inputParameters['city']) ? $inputParameters['city'] : null).
            (isset($inputParameters['postcode']) ? $inputParameters['postcode'] : null).
            (isset($inputParameters['phone']) ? $inputParameters['phone'] : null).
            (isset($inputParameters['country']) ? $inputParameters['country'] : null).
            (isset($inputParameters['code']) ? $inputParameters['code'] : null).
            (isset($inputParameters['p_info']) ? $inputParameters['p_info'] : null).
            (isset($inputParameters['p_email']) ? $inputParameters['p_email'] : null).
            (isset($inputParameters['n_email']) ? $inputParameters['n_email'] : null).
            (isset($inputParameters['expiration_date']) ? $inputParameters['expiration_date'] : null).
            (isset($inputParameters['deladdr']) ? $inputParameters['deladdr'] : null).
            (isset($inputParameters['recipient_account_number']) ? $inputParameters['recipient_account_number'] : null).
            (isset($inputParameters['recipient_company']) ? $inputParameters['recipient_company'] : null).
            (isset($inputParameters['recipient_first_name']) ? $inputParameters['recipient_first_name'] : null).
            (isset($inputParameters['recipient_last_name']) ? $inputParameters['recipient_last_name'] : null).
            (isset($inputParameters['recipient_address_street']) ? $inputParameters['recipient_address_street'] : null).
            (isset($inputParameters['recipient_address_building']) ? $inputParameters['recipient_address_building'] : null).
            (isset($inputParameters['recipient_address_apartment']) ? $inputParameters['recipient_address_apartment'] : null).
            (isset($inputParameters['recipient_address_postcode']) ? $inputParameters['recipient_address_postcode'] : null).
            (isset($inputParameters['recipient_address_city']) ? $inputParameters['recipient_address_city'] : null).
            (isset($inputParameters['application']) ? $inputParameters['application'] : null).
            (isset($inputParameters['application_version']) ? $inputParameters['application_version'] : null).
            (isset($inputParameters['warranty']) ? $inputParameters['warranty'] : null).
            (isset($inputParameters['bylaw']) ? $inputParameters['bylaw'] : null).
            (isset($inputParameters['personal_data']) ? $inputParameters['personal_data'] : null).
            (isset($inputParameters['credit_card_number']) ? $inputParameters['credit_card_number'] : null).
            (isset($inputParameters['credit_card_expiration_date_year']) ? $inputParameters['credit_card_expiration_date_year'] : null).
            (isset($inputParameters['credit_card_expiration_date_month']) ? $inputParameters['credit_card_expiration_date_month'] : null).
            (isset($inputParameters['credit_card_security_code']) ? $inputParameters['credit_card_security_code'] : null).
            (isset($inputParameters['credit_card_store']) ? $inputParameters['credit_card_store'] : null).
            (isset($inputParameters['credit_card_store_security_code']) ? $inputParameters['credit_card_store_security_code'] : null).
            (isset($inputParameters['credit_card_customer_id']) ? $inputParameters['credit_card_customer_id'] : null).
            (isset($inputParameters['credit_card_id']) ? $inputParameters['credit_card_id'] : null).
            (isset($inputParameters['blik_code']) ? $inputParameters['blik_code'] : null).
            (isset($inputParameters['credit_card_registration']) ? $inputParameters['credit_card_registration'] : null).
            (isset($inputParameters['recurring_frequency']) ? $inputParameters['recurring_frequency'] : null).
            (isset($inputParameters['recurring_interval']) ? $inputParameters['recurring_interval'] : null).
            (isset($inputParameters['recurring_start']) ? $inputParameters['recurring_start'] : null).
            (isset($inputParameters['recurring_count']) ? $inputParameters['recurring_count'] : null).
            (isset($inputParameters['surcharge_amount']) ? $inputParameters['surcharge_amount'] : null).
            (isset($inputParameters['surcharge']) ? $inputParameters['surcharge'] : null).
            (isset($inputParameters['ignore_last_payment_channel']) ? $inputParameters['ignore_last_payment_channel'] : null).
            (isset($inputParameters['customer']) ? $inputParameters['customer'] : null).
			(isset($ParametersArray['gp_token']) ? $ParametersArray['gp_token'] : null).
			(isset($ParametersArray['auto_reject_date']) ? $ParametersArray['auto_reject_date'] : null).    
            (isset($ParametersArray['ap_token']) ? $ParametersArray['ap_token'] : null);

        foreach ($subPayments as $subPayment) {
            if ($subPayment instanceof Payment) {
                $CHkInputString .= $subPayment->getId().
                                   $subPayment->getAmount().
                                   $subPayment->getCurrency().
                                   $subPayment->getDescription().
                                   $subPayment->getId();
            } else {
                throw new IncompatibleTypeException(get_class($subPayment).' != '.Payment::class);
            }
        }

        return hash('sha256', $CHkInputString);

    }     

    /**
     * Set the seller model with the correct data from plugin Configuration.
     */
    protected function chooseSeller()
    {
        $this->seller = Loader::load()->get('Seller', [
            $this->config->getId(),
            $this->config->getPin(),
            $this->config->getTestMode(),
        ]);
    }

    /**
     * Retrieve informations about the channel from Dotpay server.
     *
     * @param int|null $channelId Code number of payment channel in Dotpay system
     *
     * @throws ChannelIdException Thrown if the given channel id isn't correct
     */
    protected function setChannelInfo($channelId = null)
    {
        if ($channelId === null) {
            $this->available = false;

            return;
        }
        if ($channelId !== null && !ChannelId::validate($channelId)) {
            throw new ChannelIdException($channelId);
        }
        try {
            $channelsData = $this->paymentResource->getChannelListForTransaction($this->transaction);
            $this->channelInfo = $channelsData->getChannelInfo($channelId);
            $this->agreements = $channelsData->getAgreements($channelId);
            $this->available = true;
        } catch (NotFoundException $e) {
            $this->available = false;
        }
    }
}