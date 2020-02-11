<?php

/**
 * Copyright © 2016 Commerce Extensions. All rights reserved.
 */

namespace CommerceExtensions\GuestToReg\Plugin;

class PaypalExpressCheckout
{
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
		\Magento\Sales\Model\Order $order,
		\Magento\Framework\App\ResourceConnection $resource,
		\CommerceExtensions\GuestToReg\Model\Rewrite\FrontCheckoutTypeOnepage $converter
    ) {
		$this->_customer = $customer;
		$this->_store = $store;
		$this->_logger = $logger;
		$this->_moduleData = $moduleData;
		$this->_address = $address;
		$this->_order = $order;
		$this->_resource = $resource;
		$this->_converter = $converter;
    }


	public function saveOrderFromGuest($order)
    {

		$allow_guesttoreg_at_checkout = $this->_moduleData->moduleActive();

		if ($allow_guesttoreg_at_checkout == 1) {
		
		$entity_id = $order->getData('entity_id');
		
		$store_id = $this->getStoreId();
		$valueid = $this->_store->load($store_id)->getWebsiteId();
		//DUPLICATE CUSTOMERS are appearing after import this value above is likely not found.. so we have a little check here
		if($valueid < 1) { $valueid =1; }
		#exit;

		$customer = $this->_customer->setWebsiteId($valueid)->loadByEmail($order->getCustomerEmail());
		
			if ($customer->getId()) {
				$customerId = $customer->getId();
				$merged_customer_group_id = $customer->getGroupId();
				if($merged_customer_group_id == "") {
					$merged_customer_group_id = 1;
				}
				
			/* SOME DIRECT SQL HERE. JUST MOVES THE ORDER OVER TO THE NEWLY CREATED CUSTOMER */
			$write = $this->_resource->getConnection('core_write');
			$read = $this->_resource->getConnection('core_read');
			$select_qry = "SELECT * FROM ".$this->_resource->getTableName('sales_order')." WHERE customer_id IS NULL AND customer_email = '".$order->getCustomerEmail()."'";

			$rows = $read->fetchAll($select_qry);
				foreach($rows as $datafromexisting)
				{ 
					$existingorder_entity_id = $datafromexisting['entity_id'];

					//$write_qry = $write->query("UPDATE ".$this->_resource->getTableName('sales_order')." SET customer_id = '". $customerId ."', customer_is_guest = '0', customer_group_id = '".$merged_customer_group_id."' WHERE entity_id = '". $existingorder_entity_id ."'");
					$write_qry = $write->update($this->_resource->getTableName('sales_order'), array('customer_id' => $customerId, 'customer_is_guest' => '0', 'customer_group_id' => $merged_customer_group_id), array("entity_id = ?" => $existingorder_entity_id));
					
					//$write_qry = $write->query("UPDATE ".$this->_resource->getTableName('sales_order_grid')." SET customer_id = '". $customerId ."' WHERE entity_id = '". $existingorder_entity_id ."'");
					$write_qry = $write->update($this->_resource->getTableName('sales_order_grid'), array('customer_id' => $customerId), array("entity_id = ?" => $existingorder_entity_id));
					//UPDATE FOR DOWNLOADABLE PRODUCTS
					//$write_qry = $write->query("UPDATE ".$this->_resource->getTableName('downloadable_link_purchased')." SET customer_id = '". $customerId ."' WHERE order_id = '". $existingorder_entity_id ."'");
					$write_qry = $write->update($this->_resource->getTableName('downloadable_link_purchased'), array('customer_id' => $customerId), array("order_id = ?" => $existingorder_entity_id));
				}
			
			} else {
	 
			$write = $this->_resource->getConnection('core_write');
			$read = $this->_resource->getConnection('core_read');
			$select_qry5 = $read->query("SELECT subscriber_status FROM ".$this->_resource->getTableName('newsletter_subscriber')." WHERE subscriber_email = '". $order->getCustomerEmail() ."'");
			$newsletter_subscriber_status = $select_qry5->fetch();
			
			$merged_customer_group_id = $this->_moduleData->getConfig('merged_customer_group');
			if($merged_customer_group_id == "") {
				$merged_customer_group_id = 1;
			}
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
				
			$customerId = $this->_converter->_CreateCustomerFromGuest($order->getBillingAddress()->getData('company'), $order->getBillingAddress()->getData('city'), $order->getBillingAddress()->getData('telephone'), $order->getBillingAddress()->getData('fax'), $order->getCustomerEmail(), $order->getBillingAddress()->getData('prefix'), $fn, $middlename="", $ln, $suffix="", $taxvat="", $order->getBillingAddress()->getStreet(1), $order->getBillingAddress()->getStreet(2), $order->getBillingAddress()->getData('postcode'), $order->getBillingAddress()->getData('region'), $order->getBillingAddress()->getData('country_id'), $merged_customer_group_id, $store_id, $order->getCustomerDob(), $order->getCustomerGender());

			//$write_qry = $write->query("UPDATE ".$this->_resource->getTableName('sales_order')." SET customer_id = '". $customerId ."', customer_is_guest = '0', customer_group_id = '".$merged_customer_group_id."' WHERE entity_id = '". $entity_id ."'");
			$write_qry = $write->update($this->_resource->getTableName('sales_order'), array('customer_id' => $customerId, 'customer_is_guest' => '0', 'customer_group_id' => $merged_customer_group_id), array("entity_id = ?" => $entity_id));
			//$write_qry = $write->query("UPDATE ".$this->_resource->getTableName('sales_order_grid')." SET customer_id = '". $customerId ."' WHERE entity_id = '". $entity_id ."'");
			
			$write_qry = $write->update($this->_resource->getTableName('sales_order_grid'), array('customer_id' => $customerId), array("entity_id = ?" => $entity_id));
			//UPDATE FOR DOWNLOADABLE PRODUCTS
			//$write_qry = $write->query("UPDATE ".$this->_resource->getTableName('downloadable_link_purchased')." SET customer_id = '". $customerId ."' WHERE order_id = '". $entity_id ."'");
			$write_qry = $write->update($this->_resource->getTableName('downloadable_link_purchased'), array('customer_id' => $customerId), array("order_id = ?" => $entity_id));
			//UPDATE FOR NEWSLETTER
			if($newsletter_subscriber_status['subscriber_status'] !="" && $newsletter_subscriber_status['subscriber_status'] > 0) {
				//$write_qry = $write->query("UPDATE ".$this->_resource->getTableName('newsletter_subscriber')." SET subscriber_status = '". $newsletter_subscriber_status['subscriber_status'] ."' WHERE subscriber_email = '". $order->getCustomerEmail() ."'");
				$write_qry = $write->update($this->_resource->getTableName('newsletter_subscriber'), array('subscriber_status' => $newsletter_subscriber_status['subscriber_status']), array("subscriber_email = ?" => $order->getCustomerEmail()));
			 }
			
			}
		}
    }
	
	public function getStoreId(){
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		$manager = $om->get('Magento\Store\Model\StoreManagerInterface');
		return $manager->getStore()->getStoreId();
	}
    /**
     * Retreive new incrementId
     *
     * @param int $storeId
     * @return string
     */
    public function afterPlace(\Magento\Paypal\Model\Express\Checkout $subject, $result)
    {
		#$this->_logger->addDebug("we made it to paypal via plugin");
		$order = $this->_order;
		$getorderId = $subject->getOrder();
		try {
			$orderModel = $order->load($getorderId->getId());
			#$this->_logger->addDebug("we made it to ORDERID: ". $getorderId->getId());
        	$this->saveOrderFromGuest($orderModel);
		}
		catch (\Exception $e) { 
			#echo "<pre>"; print_r($e); exit;
           	throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
		}
		
        return $result;
    }
}
