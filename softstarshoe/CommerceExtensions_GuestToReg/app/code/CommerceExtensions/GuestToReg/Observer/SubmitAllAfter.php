<?php
/**
 * Copyright © 2016 CommerceExtensions . All rights reserved.
 */
namespace CommerceExtensions\GuestToReg\Observer;
 
use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer as EventObserver;

class SubmitAllAfter implements ObserverInterface
{   

	protected $_moduleData;
	
	protected $_logger;
	
	protected $_converter;
	
	protected $_resource;
	
	protected $_customer;
	
	public function __construct (
		\Magento\Customer\Model\Customer $customer,
		\CommerceExtensions\GuestToReg\Helper\Data $moduleData,
		\Psr\Log\LoggerInterface $logger,
		\Magento\Framework\App\ResourceConnection $resource,
		\CommerceExtensions\GuestToReg\Model\Rewrite\FrontCheckoutTypeOnepage $converter
	) {
		$this->_customer = $customer;
		$this->_moduleData = $moduleData;
		$this->_logger = $logger;
		$this->_resource = $resource;
		$this->_converter = $converter;
	}

    public function execute(EventObserver $observer)
    {
		$quote = $observer->getEvent()->getQuote();
		if($observer->getEvent()->getOrders()) {
		  foreach($observer->getEvent()->getOrders() as $order) {
			$this->_convertGuestToRegCustomer($quote, $order);
		  }
		} else {
		   $this->_convertGuestToRegCustomer($quote, $observer->getEvent()->getOrder());
		}
		return $this;
    }
	
	public function getStoreId(){
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		$manager = $om->get('Magento\Store\Model\StoreManagerInterface');
		return $manager->getStore()->getStoreId();
	}
	
	public function getWebsiteId(){
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		$manager = $om->get('Magento\Store\Model\StoreManagerInterface');
		return $manager->getWebsite()->getWebsiteId();
	}
	
	protected function _convertGuestToRegCustomer( $quote, $order )
	{
      
		$allow_guesttoreg_at_checkout = $this->_moduleData->moduleActive();
		if($allow_guesttoreg_at_checkout == 1) {
		
		#$this->_logger->log(100,print_r("SUBMIT ALL AFTER MODULE VALID",true));
		
		$entity_id = $order->getData('entity_id');
		$store_id = $this->getStoreId();
		$valueid = $this->getWebsiteId();
		if($valueid < 1) {
			$valueid = 1;
		}
		$isNewCustomer = true;
			
	  	switch($quote->getCheckoutMethod()) {
			#case \Magento\Checkout\Model\Type\Onepage::METHOD_REGISTER:
			case \CommerceExtensions\GuestToReg\Model\Rewrite\FrontCheckoutTypeOnepage::METHOD_REGISTER:
		  	$isNewCustomer = false;
		  	break;
	 	}
		  
		if($isNewCustomer) {
			$customer = $this->_customer->setWebsiteId($valueid)->loadByEmail($order->getCustomerEmail());
			if($customer->getId()) {
				$customerId = $customer->getId();
				$groupId = $customer->getGroupId();
				
				$write = $this->_resource->getConnection('core_write');
				$read = $this->_resource->getConnection('core_read');
				
				//$write_qry = $write->query("UPDATE " . $this->_resource->getTableName('sales_order') . " SET customer_id = '" . $customerId . "', customer_is_guest = '0', customer_group_id = '".$groupId."' WHERE entity_id = '" . $entity_id . "'");
				
				$write_qry = $write->update($this->_resource->getTableName('sales_order'), array('customer_id' => $customerId, 'customer_is_guest' => '0', 'customer_group_id' => $groupId), array("entity_id = ?" => $entity_id));
				
				///$write_qry = $write->query("UPDATE " . $this->_resource->getTableName('sales_order_grid') . " SET customer_id = '" . $customerId . "' WHERE entity_id = '" . $entity_id . "'");
				$write_qry = $write->update($this->_resource->getTableName('sales_order_grid'), array('customer_id' => $customerId), array("entity_id = ?" => $entity_id));

				// UPDATE FOR DOWNLOADABLE PRODUCTS
				//$write_qry = $write->query("UPDATE " . $this->_resource->getTableName('downloadable_link_purchased') . " SET customer_id = '" . $customerId . "' WHERE order_id = '" . $entity_id . "'");
				$write_qry = $write->update($this->_resource->getTableName('downloadable_link_purchased'), array('customer_id' => $customerId), array("order_id = ?" => $entity_id));
				
			}else{
				$write = $this->_resource->getConnection('core_write');
				$read = $this->_resource->getConnection('core_read');
				$groupId = $this->_moduleData->getConfig('merged_customer_group');
				
				$select_qry5 = $read->query("SELECT subscriber_status FROM " . $this->_resource->getTableName('newsletter_subscriber') . " WHERE subscriber_email = '" . $order->getCustomerEmail() . "'");
				
				$newsletter_subscriber_status = $select_qry5->fetch();
				
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
				$customerId = $this->_converter->_CreateCustomerFromGuest($order->getBillingAddress()->getData('company'), $order->getBillingAddress()->getData('city'), $order->getBillingAddress()->getData('telephone'), $order->getBillingAddress()->getData('fax'), $order->getCustomerEmail(), $order->getBillingAddress()->getData('prefix'), $fn, $middlename = "", $ln, $suffix = "", $taxvat = "", $order->getBillingAddress()->getStreet(1), $order->getBillingAddress()->getStreet(2), $order->getBillingAddress()->getData('postcode'), $order->getBillingAddress()->getData('region'), $order->getBillingAddress()->getData('country_id'), $groupId, $store_id, $order->getCustomerDob(), $order->getCustomerGender());
				
				#$this->_logger->log(100,print_r("CUSTOMER ID",true));
				#$this->_logger->log(100,print_r($customerId,true));
				
				//$write_qry = $write->query("UPDATE " . $this->_resource->getTableName('sales_order') . " SET customer_id = '" . $customerId . "', customer_is_guest = '0', customer_group_id = '".$groupId."' WHERE entity_id = '" . $entity_id . "'");
				
				$write_qry = $write->update($this->_resource->getTableName('sales_order'), array('customer_id' => $customerId, 'customer_is_guest' => '0', 'customer_group_id' => $groupId), array("entity_id = ?" => $entity_id));
				//$write_qry = $write->query("UPDATE " . $this->_resource->getTableName('sales_order_grid') . " SET customer_id = '" . $customerId . "' WHERE entity_id = '" . $entity_id . "'");
				$write_qry = $write->update($this->_resource->getTableName('sales_order_grid'), array('customer_id' => $customerId), array("entity_id = ?" => $entity_id));

				//$write_qry = $write->query("UPDATE " . $this->_resource->getTableName('downloadable_link_purchased') . " SET customer_id = '" . $customerId . "' WHERE order_id = '" . $entity_id . "'");
				$write_qry = $write->update($this->_resource->getTableName('downloadable_link_purchased'), array('customer_id' => $customerId), array("order_id = ?" => $entity_id));
				// UPDATE FOR NEWSLETTER
				if($newsletter_subscriber_status['subscriber_status'] != "" && $newsletter_subscriber_status['subscriber_status'] > 0) {
				//$write_qry = $write->query("UPDATE " . $this->_resource->getTableName('newsletter_subscriber') . " SET subscriber_status = '" . $newsletter_subscriber_status ['subscriber_status'] . "' WHERE subscriber_email = '" . $order->getCustomerEmail() . "'");
				
				$write_qry = $write->update($this->_resource->getTableName('newsletter_subscriber'), array('subscriber_status' => $newsletter_subscriber_status['subscriber_status']), array("subscriber_email = ?" => $order->getCustomerEmail()));
				}
			}
		  }
	
			$order->setCustomerId($customer->getId());
			$order->setCustomerIsGuest(false);//$order->setCustomerIsGuest('0');
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
		
		return $this;
	}
}