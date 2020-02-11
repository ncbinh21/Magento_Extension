<?php
/**
 * Copyright © 2016 CommerceExtensions. All rights reserved.
 */
namespace CommerceExtensions\GuestToReg\Controller\Adminhtml\GuestToRegForm;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class massConvert extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
	
	protected $_resource;
	
	protected $_moduleData;
	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $_storeManager;
 
    /**
     * @param \Magento\Backend\App\Action\Context        $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
		\Magento\Framework\App\ResourceConnection $resource,
		\CommerceExtensions\GuestToReg\Helper\Data $moduleData,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
    ) 
    {
		$this->_resource = $resource;
		$this->_moduleData = $moduleData;
		$this->_storeManager = $storeManager;
        $this->_resultPageFactory = $resultPageFactory;
        $this->subscriberFactory = $subscriberFactory;
        parent::__construct($context);
    }
 
    /**
     * Grid List page.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
         $orderIds = $this->getRequest()->getPost('order_ids');
         $groupId = $this->getRequest()->getPost('group_id');
        if (! $orderIds)
        {
            $this->messageManager->addError('No Order ID found to convert');
            $this->_redirect('*/*/index');
            return;
        }

        foreach ($orderIds as $orderId)
        {
            $this->convertAction($orderId, $groupId, true);
        }

        $this->_redirect('*/*/index');

    }
	
	public function rand_string($length) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		return substr(str_shuffle($chars), 0, $length);
	}
	
	public function convertAction($orderId = NULL, $groupId = NULL, $isMass = false){

		if($orderId == "") {
       	    $orderId = $this->getRequest()->getParam('order_id');
		}
		if($groupId == "") {
        	$groupId = $this->getRequest()->getParam('group_id');
		}
		try {
            // load order by Id
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $order = $objectManager->create('Magento\Sales\Model\Order')->load($orderId);

            //Resource Connection  $write (core_write) , $read (core_read)
            $write = $this->_resource->getConnection('core_write');
            $read = $this->_resource->getConnection('core_read');

            //UPDATE FOR NEWSLETTER START
            $select_qry5 = $read->query("SELECT subscriber_status FROM " . $this->_resource->getTableName('newsletter_subscriber') . " WHERE subscriber_email = '" . $order->getCustomerEmail() . "'");
            $newsletter_subscriber_status = $select_qry5->fetch();
            //UPDATE FOR NEWSLETTER END

            if (!$order->getId()) {
                $this->messageManager->addError('No Order ID found to convert');
                $this->_redirect('*/*/index');
                return;
            }
            $customer = $objectManager->create('Magento\Customer\Model\Customer')->setWebsiteId($order->getStore()->getWebsiteId())->loadByEmail($order->getCustomerEmail());

            if ($customer->getId()) {
                //UPDATE FOR DOWNLOADABLE PRODUCTS
                //$write_qry = $write->query("UPDATE ".$this->_resource->getTableName('downloadable_link_purchased')." SET customer_id = '". $customer->getId() ."' WHERE order_id = '". $order->getId() ."'");
                $write_qry = $write->update($this->_resource->getTableName('downloadable_link_purchased'), array('customer_id' => $customer->getId()), array("order_id = ?" => $order->getId()));
                //UPDATE FOR NEWSLETTER START
                if ($newsletter_subscriber_status['subscriber_status'] != "" && $newsletter_subscriber_status['subscriber_status'] > 0) {
                    //$write_qry = $write->query("UPDATE ".$this->_resource->getTableName('newsletter_subscriber')." SET subscriber_status = '". $newsletter_subscriber_status['subscriber_status'] ."' WHERE subscriber_email = '". $order->getCustomerEmail() ."'");
                    $write_qry = $write->update($this->_resource->getTableName('newsletter_subscriber'), array('subscriber_status' => $newsletter_subscriber_status['subscriber_status']), array("subscriber_email = ?" => $order->getCustomerEmail()));
                }
                //UPDATE FOR NEWSLETTER END

                $this->messageManager->addSuccess('The customer (%s) already exists. So the customer has been merged', $order->getCustomerEmail());
            } else { //create a new customer based on the order

                /** @var $billingAddress from order */
                $billingAddress = $order->getBillingAddress();
                /** @var $shippingAddress from order */
                $shippingAddress = $order->getShippingAddress();

                // @var $fn (Customer first name) , @var $ln (Customer last name)
                $fn = $order->getCustomerFirstname();
                $ln = $order->getCustomerLastname();

                if (!$fn || !$ln) {
                    foreach (array($billingAddress, $shippingAddress) as $t) {
                        if ($t->getFirstname() && $t->getLastname()) {
                            $fn = $t->getFirstname();
                            $ln = $t->getLastname();
                            break;
                        }
                    }
                }

                if (!$fn || !$ln) {
                    $fn = $fn || "GUEST";
                    $ln = $ln || "GUEST";

                    $this->messageManager->addNotice("Customer name missing from sales order and all addresses (email: " . $order->getCustomerEmail() . ").  Setting to '$fn $ln'");
                }

                $customer->addData(array(
                    "prefix" => $order->getCustomerPrefix(),
                    "firstname" => $fn,
                    "middlename" => $order->getCustomerMiddlename(),
                    "lastname" => $ln,
                    "suffix" => $order->getCustomerSuffix(),
                    "dob" => $order->getCustomerDob(),
                    "gender" => $order->getCustomerGender(),
                    "email" => $order->getCustomerEmail(),
                    "group_id" => $groupId,
                    "taxvat" => $order->getCustomerTaxvat(),
                    "website_id" => $order->getStore()->getWebsiteId(),
                    'default_billing' => '_item1',
                    'default_shipping' => '_item2',
                ));


                //Billing Address
                /** @var $customerBillingAddress Magento\Customer\Model\Address */
                $customerBillingAddress = $objectManager->create('Magento\Customer\Model\Address');

                $billingAddressArray = $billingAddress->toArray();
                unset($billingAddressArray['entity_id']);
                unset($billingAddressArray['entity_type_id']);
                unset($billingAddressArray['parent_id']);
                unset($billingAddressArray['customer_id']);
                unset($billingAddressArray['customer_address_id']);
                unset($billingAddressArray['quote_address_id']);


                if (empty($billingAddressArray['telephone'])) {
                    $billingAddressArray['telephone'] = "000-000-0000";
                }

                #print_r($billingAddressArray);
                $customerBillingAddress->setIsDefaultBilling(true)
                    ->setSaveInAddressBook(true);

                $customerBillingAddress->addData($billingAddressArray);
                $customerBillingAddress->setPostIndex('_item1');
                $customer->addAddress($customerBillingAddress);

                //Shipping Address
                /** @var $customerShippingAddress Magento\Customer\Model\address */
                $customerShippingAddress = $objectManager->create('Magento\Customer\Model\Address');

                if (!empty($shippingAddress)) {
                    $shippingAddressArray = $shippingAddress->toArray();
                    unset($shippingAddressArray['entity_id']);
                    unset($shippingAddressArray['entity_type_id']);
                    unset($shippingAddressArray['parent_id']);
                    unset($shippingAddressArray['customer_id']);
                    unset($shippingAddressArray['customer_address_id']);
                    unset($shippingAddressArray['quote_address_id']);

                    if (empty($shippingAddressArray['firstname'])) {
                        $shippingAddressArray['firstname'] = $billingAddressArray['firstname'];
                    }
                    if (empty($shippingAddressArray['lastname'])) {
                        $shippingAddressArray['lastname'] = $billingAddressArray['lastname'];
                    }
                    if (empty($shippingAddressArray['telephone'])) {
                        $shippingAddressArray['telephone'] = $billingAddressArray['telephone'];
                    }
                    $customerShippingAddress->setIsDefaultShipping(true)
                        ->setSaveInAddressBook(true);

                    $customerShippingAddress->addData($shippingAddressArray);
                    $customerShippingAddress->setPostIndex('_item2');
                    $customer->addAddress($customerShippingAddress);
                }

                //Save the customer
                $customer->setIsSubscribed(false);
                //$customer->setPassword($customer->generatePassword());
                $newpassword = $this->rand_string(12);
                $customer->setPassword($newpassword);
                $customer->save();
                if($customer->getId()) {
                    $this->subscriberFactory->create()->subscribeCustomerById($customer->getId());
                }

                $disable_new_customer_email = (bool)$this->_moduleData->getConfig('disable_new_customer_email');
                if ($disable_new_customer_email != true) {
                    #$customer->sendNewAccountEmail();
                    #$customer->sendNewAccountEmail($type = 'registered', $backUrl = '',$order->getStore()->getId());

                    if (empty($customer->getRpToken())) {
                        $passwordLinkToken = $objectManager->get('Magento\User\Helper\Data')->generateResetPasswordLinkToken();
                        $customer->setRpToken($passwordLinkToken);
                        $customer->setRpTokenCreatedAt(
                            (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT)
                        );
                        $customer->save();
                        if($customer->getId()) {
                            $this->subscriberFactory->create()->subscribeCustomerById($customer->getId());
                        }
                    }
                    $emailTemplateIdentifier = $this->_moduleData->getConfig('customer_notifyemail_email_template');
                    $senderSelection = $this->_moduleData->getConfig('sender_email_identity');

                    $senderName = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('trans_email/ident_' . $senderSelection . '/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                    $senderEmail = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('trans_email/ident_' . $senderSelection . '/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

                    $emailTempVariables = array();
                    $postObject = new \Magento\Framework\DataObject();
                    $postObject->setData($emailTempVariables);

                    $sender = [
                        'name' => $senderName,
                        'email' => $senderEmail,
                    ];

                    $_transportBuilder = $objectManager->get('Magento\Framework\Mail\Template\TransportBuilder');
                    $transport = $_transportBuilder->setTemplateIdentifier($emailTemplateIdentifier)
                        ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->_storeManager->getStore()->getId()])
                        ->setTemplateVars([
                            'store' => $this->_storeManager->getStore(),
                            'data' => $postObject,
                            'customer' => $customer
                        ])
                        ->setFrom($sender)
                        ->addTo($order->getCustomerEmail())
                        ->getTransport();

                    $transport->sendMessage();
                }

                //UPDATE FOR DOWNLOADABLE PRODUCTS
                //$write_qry = $write->query("UPDATE ".$this->_resource->getTableName('downloadable_link_purchased')." SET customer_id = '". $customer->getId() ."' WHERE order_id = '". $order->getId() ."'");
                $write_qry = $write->update($this->_resource->getTableName('downloadable_link_purchased'), array('customer_id' => $customer->getId()), array("order_id = ?" => $order->getId()));

                //UPDATE FOR NEWSLETTER START
                if ($newsletter_subscriber_status['subscriber_status'] != "" && $newsletter_subscriber_status['subscriber_status'] > 0) {
                    //$write_qry = $write->query("UPDATE ".$this->_resource->getTableName('newsletter_subscriber')." SET subscriber_status = '". $newsletter_subscriber_status['subscriber_status'] ."' WHERE subscriber_email = '". $order->getCustomerEmail() ."'");
                    $write_qry = $write->update($this->_resource->getTableName('newsletter_subscriber'), array('subscriber_status' => $newsletter_subscriber_status['subscriber_status']), array("subscriber_email = ?" => $order->getCustomerEmail()));
                }
                //UPDATE FOR NEWSLETTER END

                $this->messageManager->addSuccess('The guest (%s) is converted to customer', $order->getCustomerEmail());
            }

            $order->setCustomerId($customer->getId());
            $order->setCustomerIsGuest(false);
            $order->setCustomerGroupId($groupId);
            $order->save();

            $this->messageManager->addSuccess('The order (%s) has been been assigned to the customer (%s)', $order->getIncrementId(), $order->getCustomerEmail());
        }catch (\Exception $e){
            $this->messageManager->addErrorMessage('An error occurred during processing order ('.$order->getIncrementId().'): '.$e->getMessage());
        }
        if (! $isMass) $this->_redirect('*/*/index');
        return $this;
	}
	
}