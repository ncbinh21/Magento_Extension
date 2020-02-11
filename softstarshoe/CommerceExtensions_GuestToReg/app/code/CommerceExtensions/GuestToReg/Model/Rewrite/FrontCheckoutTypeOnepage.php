<?php
/**
 * Copyright © 2016 CommerceExtensions . All rights reserved.
 */

namespace CommerceExtensions\GuestToReg\Model\Rewrite;

class FrontCheckoutTypeOnepage extends \Magento\Checkout\Model\Type\Onepage
{
	const METHOD_REGISTER = 'register';
	
	protected $_customer;
	
	protected $_store;
	
	protected $_address;
	
	protected $_order;
	
	protected  $_resource;
	
	protected  $_eventManager;
	
	protected $_logger;
	
	protected $_moduleData;
	
    public function __construct(
		\Magento\Customer\Model\Customer $customer,
		\Magento\Store\Model\Store $store,
		\Psr\Log\LoggerInterface $logger,
		\CommerceExtensions\GuestToReg\Helper\Data $moduleData,
		\Magento\Customer\Model\Address $address,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $addressFactory,
        \Magento\Customer\Model\ResourceModel\AddressRepository $addressRepository,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
		\Magento\Sales\Model\Order $order,
		\Magento\Framework\App\ResourceConnection $resource
    ) 
    {
		$this->_customer = $customer;
		$this->_store = $store;
		$this->_logger = $logger;
		$this->_moduleData = $moduleData;
		$this->_address = $address;
		$this->_addressFactory = $addressFactory;
		$this->_addressRepository = $addressRepository;
        $this->_countryFactory = $countryFactory;
        $this->_regionFactory = $regionFactory;
		$this->_order = $order;
		$this->_resource = $resource;
    }
	
	 public function rand_string($length) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		return substr(str_shuffle($chars), 0, $length);
	 }
	 
	 public function getRegionId($billing_region, $billing_country)
     {
        /** @var \Magento\Directory\Model\Region $region */
        $region = $this->_regionFactory->create();
        $region->loadByName($billing_region, $billing_country);
		$id = "";
        if($region->getId()) {
            $id = $region->getId();
        }
        return $id;
     }
	 
	 public function _CreateCustomerFromGuest($company, $city, $telephone, $fax="", $email, $prefix="", $firstname, $middlename="", $lastname, $suffix="", $taxvat="", $street1, $street2="", $postcode, $region_id, $country_id, $customer_group_id, $storeId, $customer_dob, $customer_gender) {
				$customer = $this->_customer;
				$street_r=array("0"=>$street1,"1"=>$street2);
				$group_id=$customer_group_id;
				$website_id = $this->_store->load($storeId)->getWebsiteId();
						
				$customerData=array(
						"prefix"=>$prefix,
						"firstname"=>$firstname,
						"middlename"=>$middlename,
						"lastname"=>$lastname,
						"suffix"=>$suffix,
                		"dob"=>$customer_dob,
                		"gender"=>$customer_gender,
						"email"=>$email,
						"group_id"=>$group_id,
						"taxvat"=>$taxvat,
						"website_id"=>$website_id,
						"default_billing"=> "_item1",
						'default_shipping'=> "_item2",
				);
				$customer->addData($customerData); ///make sure this is enclosed in arrays correctly
		
				$addressData=array(
						"prefix"=>$prefix,
						"firstname"=>$firstname,
						"middlename"=>$middlename,
						"lastname"=>$lastname,
						"suffix"=>$suffix,
						"company"=>$company,
						"street"=>$street_r,
						"city"=>$city,
						"region"=>$region_id,
						"country_id"=>$country_id,
						"postcode"=>$postcode,
						"telephone"=>$telephone,
						"fax"=>$fax
				);
				
			    $newpassword = $this->rand_string(12);
				$customer->setPassword($newpassword);
				
				///adminhtml_customer_prepare_save
				$customer->save();
				
        		$address = $this->_addressFactory->create();
				$address->setCustomerId($customer->getId());
				$address->setCompany($addressData['company']);
				$address->setPrefix($addressData['prefix']);
				$address->setFirstname($addressData['firstname']);
				$address->setMiddlename($addressData['middlename']);
				$address->setLastname($addressData['lastname']);
				$address->setSuffix($addressData['suffix']);
				$address->setStreet(array(implode(" ",$street1)));
				$address->setCity($addressData['city']);
				$billingRegionId = $this->getRegionId($addressData['region'],$addressData['country_id']);
				$address->setRegionId($billingRegionId);
				$address->setCountryId($addressData['country_id']);
				$address->setPostcode($addressData['postcode']);
				$address->setTelephone($addressData['telephone']);
				$address->setFax($addressData['fax']);
				$address->setIsDefaultBilling(true);
				$address->setIsDefaultShipping(true);
				
				try {
					$this->_addressRepository->save($address);
				} catch(\Exception $e) {
					$this->_logger->log(100,print_r("ERROR:" .$e->getMessage(),true));
				}
				
			    $disable_new_customer_email = (bool)$this->_moduleData->getConfig('disable_new_customer_email');
				if ($disable_new_customer_email != true) {
          			#$customer->sendNewAccountEmail($type = 'registered', $backUrl = '',$storeId);
					$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
					/*
					if(empty($customer->getRpToken())) {
						 $passwordLinkToken = $objectManager->get('Magento\User\Helper\Data')->generateResetPasswordLinkToken();
						 $customer->setRpToken($passwordLinkToken);
						 $customer->setRpTokenCreatedAt(
							(new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT)
						 );
						 $customer->save();
					 }
					 */
					 $emailTemplateIdentifier = $this->_moduleData->getConfig('customer_notifyemail_email_template');
					 $senderSelection = $this->_moduleData->getConfig('sender_email_identity');
	
					 $senderName = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('trans_email/ident_'.$senderSelection.'/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
					 $senderEmail = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('trans_email/ident_'.$senderSelection.'/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	
					 $emailTempVariables = array();
					 $postObject = new \Magento\Framework\DataObject();
					 $postObject->setData($emailTempVariables);
					
					 $sender = [
								'name' => $senderName,
								'email' => $senderEmail,
								];
					
					 $_transportBuilder = $objectManager->get('Magento\Framework\Mail\Template\TransportBuilder');
					 $transport = $_transportBuilder->setTemplateIdentifier($emailTemplateIdentifier)
													->setTemplateOptions(['area'=>\Magento\Framework\App\Area::AREA_FRONTEND, 'store'=>$storeId])
													->setTemplateVars([
																	   'store' => $this->_store->load($storeId),
																	   'data' => $postObject,
																	   'customer' => $customer
																	  ])
													->setFrom($sender)
													->addTo($email)         
													->getTransport();           
						
					$transport->sendMessage();
				}
				
				///adminhtml_customer_save_after
				$customerId=$customer->getId();
				#$this->_logger->log(100,print_r($customerId,true));

				return $customerId;
	} 
		
    public function saveOrder()
    {
        
        $oResult = parent::saveOrder();
		$allow_guesttoreg_at_checkout = $this->_moduleData->moduleActive();
		$order = $this->_order;
		$order->load($this->getCheckout()->getLastOrderId());
		
		if (($allow_guesttoreg_at_checkout == 1) || ($this->isOrderPaypal($order))) {
		
		$entity_id = $order->getData('entity_id');
		$groupId = $this->_moduleData->getConfig('merged_customer_group');
		if($groupId == "") { $groupId = 1; }

		$store_id = $this->getStoreId();
		$valueid = $this->_store->load($store_id)->getWebsiteId();
		//DUPLICATE CUSTOMERS are appearing after import this value above is likely not found.. so we have a little check here
		if($valueid < 1) { $valueid =1; }
		
		$isNewCustomer = true;
        switch (parent::getCheckoutMethod()) {
            case self::METHOD_REGISTER:
                $isNewCustomer = false;
                break;
        }

        if ($isNewCustomer) {
		
			$customer = $this->_customer->setWebsiteId($valueid)->loadByEmail($order->getCustomerEmail());
			
			if ($customer->getId()) {
				$customerId = $customer->getId();
				$groupId = $customer->getGroupId();
				
				$write = $this->_resource->getConnection('core_write');
				$read = $this->_resource->getConnection('core_read');
				$select_qry5 = $read->query("SELECT subscriber_status FROM ".$this->_resource->getTableName('newsletter_subscriber')." WHERE subscriber_email = '". $order->getCustomerEmail() ."'");
				$newsletter_subscriber_status = $select_qry5->fetch();
	
				//UPDATE FOR DOWNLOADABLE PRODUCTS
				//$write_qry = $write->query("UPDATE ".$this->_resource->getTableName('downloadable_link_purchased')." SET customer_id = '". $customer->getId() ."' WHERE order_id = '". $order->getId() ."'");
				$write_qry = $write->update($this->_resource->getTableName('downloadable_link_purchased'), array('customer_id' => $customer->getId()), array("order_id = ?" => $order->getId()));
				
				//UPDATE FOR NEWSLETTER START
				if($newsletter_subscriber_status['subscriber_status'] !="" && $newsletter_subscriber_status['subscriber_status'] > 0) {
				//$write_qry = $write->query("UPDATE ".$this->_resource->getTableName('newsletter_subscriber')." SET subscriber_status = '". $newsletter_subscriber_status['subscriber_status'] ."' WHERE subscriber_email = '". $order->getCustomerEmail() ."'");
				$write_qry = $write->update($this->_resource->getTableName('newsletter_subscriber'), array('subscriber_status' => $newsletter_subscriber_status['subscriber_status']), array("subscriber_email = ?" => $order->getCustomerEmail()));
				}
				//UPDATE FOR NEWSLETTER END
			
			} else {
			
			//check for customer first and last name
				
			/** @var $billingAddress from order */
			$billingAddress = $order->getBillingAddress();
			/** @var $shippingAddress from order */
			$shippingAddress = $order->getShippingAddress();
			// @var $fn (Customer first name) , @var $ln (Customer last name)
			$fn = $order->getCustomerFirstname();
			$ln = $order->getCustomerLastname();
			
			if(!$fn || !$ln)
			{
				foreach(array($billingAddress, $shippingAddress) as $t)
				{
					if($t->getFirstname() && $t->getLastname())
					{
						$fn = $t->getFirstname();
						$ln = $t->getLastname();
						break;
					}
				}
			}

			if(!$fn || !$ln)
			{
				$fn = $fn || "GUEST";
				$ln = $ln || "GUEST";
			}
			
			//check for customer first and last name
				
			$customer->addData(array(
                "prefix"         => $order->getCustomerPrefix(),
                "firstname"      => $fn,//$order->getCustomerFirstname()
                "middlename"     => $order->getCustomerMiddlename(),
                "lastname"       => $ln,//$order->getCustomerLastname()
                "suffix"         => $order->getCustomerSuffix(),
                "dob"         	 => $order->getCustomerDob(),
                "gender"         => $order->getCustomerGender(),
                "email"          => $order->getCustomerEmail(),
                "group_id"       => $groupId,
                "taxvat"         => $order->getCustomerTaxvat(),
                "website_id"     => $order->getStore()->getWebsiteId(),
                'default_billing'=> '_item1',
                'default_shipping'=> '_item2',
            ));

            //Billing Address
            $billingAddress = $order->getBillingAddress();
            $customerBillingAddress = $this->_address;

            $billingAddressArray = $billingAddress->toArray();
            unset($billingAddressArray['entity_id']);
            unset($billingAddressArray['entity_type_id']);
            unset($billingAddressArray['parent_id']);
            unset($billingAddressArray['customer_id']);
            unset($billingAddressArray['customer_address_id']);
            unset($billingAddressArray['quote_address_id']);
			
			$customerBillingAddress->setIsDefaultBilling(true)
								   ->setSaveInAddressBook(true); 
								   
            $customerBillingAddress->addData($billingAddressArray);
            $customerBillingAddress->setPostIndex('_item1');
            $customer->addAddress($customerBillingAddress);

            //Shipping Address
            $shippingAddress = $order->getShippingAddress();
            $customerShippingAddress = $this->_address;
			
			if(!empty($shippingAddress)) {
				$shippingAddressArray = $shippingAddress->toArray();
				unset($shippingAddressArray['entity_id']);
            	unset($shippingAddressArray['entity_type_id']);
				unset($shippingAddressArray['parent_id']);
				unset($shippingAddressArray['customer_id']);
				unset($shippingAddressArray['customer_address_id']);
				unset($shippingAddressArray['quote_address_id']);
				
				$customerShippingAddress->setIsDefaultShipping(true)
										->setSaveInAddressBook(true); 
										
				$customerShippingAddress->addData($shippingAddressArray);
				$customerShippingAddress->setPostIndex('_item2');
				$customer->addAddress($customerShippingAddress);
			}
			
            //Save the customer
            $customer->setIsSubscribed(false);
			$newpassword = $this->rand_string(12);
            $customer->setPassword($newpassword);
            $customer->save();
			
			
			$disable_new_customer_email = $this->_moduleData->getConfig('disable_new_customer_email');
	        if ($disable_new_customer_email != true) {
				#$customer->sendNewAccountEmail($type = 'registered', $backUrl = '',$store_id);
				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
				/*
				if(empty($customer->getRpToken())) {
				 	 $passwordLinkToken = $objectManager->get('Magento\User\Helper\Data')->generateResetPasswordLinkToken();
					 $customer->setRpToken($passwordLinkToken);
					 $customer->setRpTokenCreatedAt(
						(new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT)
					 );
					 $customer->save();
				 }
				 */
                 $emailTemplateIdentifier = $this->_moduleData->getConfig('customer_notifyemail_email_template');
                 $senderSelection = $this->_moduleData->getConfig('sender_email_identity');

                 $senderName = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('trans_email/ident_'.$senderSelection.'/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                 $senderEmail = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('trans_email/ident_'.$senderSelection.'/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);

                 $emailTempVariables = array();
                 $postObject = new \Magento\Framework\DataObject();
                 $postObject->setData($emailTempVariables);
				
                 $sender = [
                            'name' => $senderName,
                            'email' => $senderEmail,
                            ];
				
				 $_transportBuilder = $objectManager->get('Magento\Framework\Mail\Template\TransportBuilder');
                 $transport = $_transportBuilder->setTemplateIdentifier($emailTemplateIdentifier)
												->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store'=>$storeId])
												->setTemplateVars([
																   'store' => $this->_store->load($storeId),
																   'data' => $postObject,
																   'customer' => $customer
																  ])
												->setFrom($sender)
												->addTo($order->getCustomerEmail())         
												->getTransport();           
				    
                $transport->sendMessage();
			}
			
			$write = $this->_resource->getConnection('core_write');
			$read = $this->_resource->getConnection('core_read');
			$select_qry5 = $read->query("SELECT subscriber_status FROM ".$this->_resource->getTableName('newsletter_subscriber')." WHERE subscriber_email = '". $order->getCustomerEmail() ."'");
			$newsletter_subscriber_status = $select_qry5->fetch();
			
			//UPDATE FOR DOWNLOADABLE PRODUCTS
			//$write_qry = $write->query("UPDATE ".$this->_resource->getTableName('downloadable_link_purchased')." SET customer_id = '". $customer->getId() ."' WHERE order_id = '". $order->getId() ."'");
			$write_qry = $write->update($this->_resource->getTableName('downloadable_link_purchased'), array('customer_id' => $customer->getId()), array("order_id = ?" => $order->getId()));
				
			//UPDATE FOR NEWSLETTER START
			if($newsletter_subscriber_status['subscriber_status'] !="" && $newsletter_subscriber_status['subscriber_status'] > 0) {
			//$write_qry = $write->query("UPDATE ".$this->_resource->getTableName('newsletter_subscriber')." SET subscriber_status = '". $newsletter_subscriber_status['subscriber_status'] ."' WHERE subscriber_email = '". $order->getCustomerEmail() ."'");
			$write_qry = $write->update($this->_resource->getTableName('newsletter_subscriber'), array('subscriber_status' => $newsletter_subscriber_status['subscriber_status']), array("subscriber_email = ?" => $order->getCustomerEmail()));
			}
			
			}					
		}
		
        $order->setCustomerId($customer->getId());
        $order->setCustomerIsGuest('0');
        $order->setCustomerGroupId($groupId);
		
		try {
			$order->save();
		} catch (\Exception $e) { 
           	throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
			#print_r($e->getMessage());
			#print_r($order->getData());
			#print_r($customer->getData());
		}
			
		
		}
        return $oResult;
    }   

	/**
	 * Payment methods
	 */
	
	private function isOrderPaypal($order) {
		$paymentType=false;
		$payment = $this->getOrderPayment ( $order );
		if (! $payment) {
			return $paymentType;
		}
		
		$payMeth = $payment->getData ( 'method' );
		
		switch ($payMeth) {
			
			case 'paypal_express' :
			case 'paypal_standard-removeifusing' :
				$paymentType=true;
				return $paymentType;
				break;
			case 'googlecheckout' :
			case 'google checkout' :
				$paymentType=true;
				break;
			default :
				return $paymentType;
				break;
		}
		
		return $paymentType;
	}
	
	public function getOrderPayment($order) {
		$payments = $order->getPaymentsCollection();
		$paymentArray = array ();
		foreach ( $payments->getItems () as $item ) {
			$paymentArray [] = $item;
		}
		
		$paymentMethod = $paymentArray[0];
		
		if (! $paymentMethod || ! is_object ( $paymentMethod )) {
			return false;
		}
		
		return $paymentMethod;
	
	}
	
	public function getStoreId(){
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		$manager = $om->get('Magento\Store\Model\StoreManagerInterface');
		return $manager->getStore()->getStoreId();
	}
    
}
