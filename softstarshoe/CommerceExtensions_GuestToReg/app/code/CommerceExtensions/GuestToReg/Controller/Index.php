<?php
namespace CommerceExtensions\GuestToReg\Controller\Adminhtml;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Context
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
	
	protected $_customer;
	
	protected $_store;
	
	protected $_address;
	
	protected $_order;
	
	protected  $_resource;
	
	protected  $_eventManager;
	
	protected $_logger;
	
	protected $_moduleData;
 
    /**
     * @param \Magento\Backend\App\Action\Context        $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Customer\Model\Customer $customer,
		\Magento\Store\Model\Store $store,
		\Psr\Log\LoggerInterface $logger,
		\CommerceExtensions\GuestToReg\Helper\Data $moduleData,
		\Magento\Customer\Model\Address $address,
		\Magento\Sales\Model\Order $order,
		\Magento\Framework\App\ResourceConnection $resource,
		\CommerceExtensions\GuestToReg\Model\Rewrite\FrontCheckoutTypeOnepage $converter
    ) 
    {
        $this->_resultPageFactory = $resultPageFactory;
		$this->_customer = $customer;
		$this->_store = $store;
		$this->_logger = $logger;
		$this->_moduleData = $moduleData;
		$this->_address = $address;
		$this->_order = $order;
		$this->_resource = $resource;
		$this->_converter = $converter;
    }

	public function execute() {
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function postAction() {
		if($this->getRequest()->getPost()) {
		
			#$this->_logger->log(100,print_r("INDEX CONTROLLER MODULE VALID",true));
		
			$data = $this->getRequest()->getPost();
			$write = $this->_resource->getConnection('core_write');
			$read = $this->_resource->getConnection('core_read');
			
			$select_qry =$read->query("SELECT entity_id FROM ".$this->_resource->getTableName('sales_order')." WHERE `increment_id`='".$data['customer_order_id']."'");

			$row = $select_qry->fetch();
			$entity_id = $row['entity_id'];
			
			$order = $this->_order;
			$order->load($entity_id); 
			
			$store_id = $this->getStoreId();
			$valueid = $this->_store->load($store_id)->getWebsiteId();

			if($valueid < 1) { $valueid =1; }

		
			$customer = $this->_customer->setWebsiteId($valueid)->loadByEmail($order->getCustomerEmail());
			
			if ($customer->getId()) {
			$customerexistedmessage = true;
			$customerId = $customer->getId();
			
			$merged_customer_group_id = $customer->getGroupId();
			if($merged_customer_group_id == "") {
				$merged_customer_group_id = 1;
			}
			
			/* SOME DIRECT SQL HERE. JUST MOVES THE ORDER OVER TO THE NEWLY CREATED CUSTOMER */
			 #$entityTypeId = Mage::getModel('eav/entity')->setType('order')->getTypeId();
			
			 $select_qry = "SELECT * FROM ".$this->_resource->getTableName('sales_order')." WHERE customer_id IS NULL AND customer_email = '".$order->getCustomerEmail()."'";
			 
			 $rows = $read->fetchAll($select_qry);
			 foreach($rows as $datafromexisting)
				{ 
					$existingorder_entity_id = $datafromexisting['entity_id'];

					//$write_qry = $write->query("UPDATE ".$this->_resource->getTableName('sales_order')." SET customer_id = '". $customerId ."', customer_is_guest = '0', customer_group_id = '".$merged_customer_group_id."' WHERE entity_id = '". $existingorder_entity_id ."'");
					
					$write_qry = $write->update($this->_resource->getTableName('sales_order'), array('customer_id' => $customerId, 'customer_is_guest' => '0', 'customer_group_id' => $merged_customer_group_id), array("entity_id = ?" => $existingorder_entity_id));
					
					//$write_qry = $write->query("UPDATE ".$this->_resource->getTableName('sales_order_grid')." SET customer_id = '". $customerId ."' WHERE entity_id = '". $existingorder_entity_id ."'");
					
					$write_qry = $write->update($this->_resource->getTableName('sales_order_grid'), array('customer_id' => $customerId), array("entity_id = ?" => $existingorder_entity_id));

					//$write_qry = $write->query("UPDATE ".$this->_resource->getTableName('downloadable_link_purchased')." SET customer_id = '". $customerId ."' WHERE order_id = '". $existingorder_entity_id ."'");
					
					$write_qry = $write->update($this->_resource->getTableName('downloadable_link_purchased'), array('customer_id' => $customerId), array("order_id = ?" => $existingorder_entity_id));
			
				}
		
			} else {
			$customerexistedmessage = false;

			$write = $this->_resource->getConnection('core_write');
			$read = $this->_resource->getConnection('core_read');
			
			$select_qry5 = $read->query("SELECT subscriber_status FROM ".$this->_resource->getTableName('newsletter_subscriber')." WHERE subscriber_email = '". $order->getCustomerEmail() ."'");
			$newsletter_subscriber_status = $select_qry5->fetch();
			
			$merged_customer_group_id = $this->_moduleData->getConfig('merged_customer_group');
			if($merged_customer_group_id == "") {
				$merged_customer_group_id = 1;
			}
		
			$customerId = $this->_converter->_CreateCustomerFromGuest($order->getBillingAddress()->getData('company'), $order->getBillingAddress()->getData('city'), $order->getBillingAddress()->getData('telephone'), $order->getBillingAddress()->getData('fax'), $order->getCustomerEmail(), $order->getBillingAddress()->getData('prefix'), $order->getBillingAddress()->getData('firstname'), $middlename="", $order->getBillingAddress()->getData('lastname'), $suffix="", $taxvat="", $order->getBillingAddress()->getStreet(1), $order->getBillingAddress()->getStreet(2), $order->getBillingAddress()->getData('postcode'), $order->getBillingAddress()->getData('region'), $order->getBillingAddress()->getData('country_id'), $merged_customer_group_id, $store_id, $order->getCustomerDob(), $order->getCustomerGender());
		
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
			if($customerexistedmessage == true){	
	 			$message = 'The email address already exists so the order has been merged to the existing account.';
			} else {
				$message = 'Your account has been created and a email has been sent to you with their username and password.';
			}
			$this->_logger->debug($message);
		
		}
		else {
			$this->_logger->debug('Sorry this order could not be converted to a customer account.');
		}
		
		if($this->getRequest()->getParam("backurl")!="")
			$this->_redirect($this->getRequest()->getParam("backurl"));
		else 
			$this->_redirect("/");
	}
	
	public function getStoreId(){
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		$manager = $om->get('Magento\Store\Model\StoreManagerInterface');
		return $manager->getStore()->getStoreId();
	}
	
}