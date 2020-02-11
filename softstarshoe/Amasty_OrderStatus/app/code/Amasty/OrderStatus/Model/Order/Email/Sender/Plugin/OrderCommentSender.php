<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_OrderStatus
 */


namespace Amasty\OrderStatus\Model\Order\Email\Sender\Plugin;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Container\OrderCommentIdentity;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Framework\Event\ManagerInterface;

class OrderCommentSender extends \Magento\Sales\Model\Order\Email\Sender\OrderCommentSender
{
    protected $_coreRegistry;
    protected $_objectManager;
    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $_moduleManager;
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    private $customerFactory;

    public function __construct(
        Template $templateContainer,
        OrderCommentIdentity $identityContainer,
        \Magento\Sales\Model\Order\Email\SenderBuilderFactory $senderBuilderFactory,
        \Psr\Log\LoggerInterface $logger,
        Renderer $addressRenderer,
        ManagerInterface $eventManager,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        parent::__construct($templateContainer, $identityContainer, $senderBuilderFactory,
            $logger, $addressRenderer, $eventManager);
        $this->_coreRegistry = $coreRegistry;
        $this->_objectManager = $objectManager;
        $this->_moduleManager = $moduleManager;
        $this->customerFactory = $customerFactory;
    }

    public function aroundSend($subject, $proceed, $order, $notify, $comment)
    {
        $transport = [
            'order' => $order,
            'customer' => $this->getCustomer($order),
            'comment' => $comment,
            'billing' => $order->getBillingAddress(),
            'store' => $order->getStore(),
            'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
            'formattedBillingAddress' => $this->getFormattedBillingAddress($order),
        ];

        $this->eventManager->dispatch(
            'email_order_comment_set_template_vars_before',
            ['sender' => $this, 'transport' => $transport]
        );

        $this->templateContainer->setTemplateVars($transport);

        return $this->_customCheckAndSend($order, $notify);
    }

    /**
     * @param $order
     * @return \Magento\Customer\Model\Customer
     */
    protected function getCustomer($order)
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->customerFactory->create();
        $customer->setStore($order->getStore());
        $customer->setWebsiteId($order->getStore()->getWebsite()->getId());
        if ($order->getCustomerId()) {
            $customer->loadByEmail($order->getCustomerEmail());

        } elseif ($this->_moduleManager->isEnabled('Amasty_CustomerAttributes')) {
            $model = $this->_objectManager
                ->create('Amasty\CustomerAttributes\Model\Customer\GuestAttributes')
                ->loadByOrderId($order->getId());
            if ($model && $model->getId()) {
                foreach ($model->getData() as $key => $value) {
                    if ($key == 'id') {
                        continue;
                    }
                    if ($value) {
                        $customer->setData($key, $value);
                    }
                }
            }
        }

        return $customer;
    }

    protected function _customCheckAndSend($order, $notify)
    {
        $this->identityContainer->setStore($order->getStore());
        if (!$this->identityContainer->isEnabled()) {
            return false;
        }
        $this->_prepareTemplate($order);

        /** @var SenderBuilder $sender */
        $sender = $this->getSender();

        $amastyStatus = $this->_coreRegistry->registry('amorderstatus_history_status');
        if (!is_null($amastyStatus) && !$notify) {
            $notify = $amastyStatus->getNotifyByEmail();
        }

        if ($notify) {
            $sender->send();
        } else {
            /*Email copies are sent as separated emails
             * if their copy method is 'copy' or a customer should not be notified
            */
            $sender->sendCopyTo();
        }

        return true;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     */
    protected function _prepareTemplate($order)
    {
        $this->templateContainer->setTemplateOptions($this->getTemplateOptions());

        if ($order->getCustomerIsGuest()) {
            $templateId = $this->identityContainer->getGuestTemplateId();
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $templateId = $this->identityContainer->getTemplateId();
            $customerName = $order->getCustomerName();
        }

        /** @var \Amasty\OrderStatus\Model\Status $ourStatus */
        $ourStatus = $this->_coreRegistry->registry('amorderstatus_history_status');
        if ($ourStatus) {
            $storeId = $order->getStoreId();
            
            /** @var \Amasty\OrderStatus\Model\ResourceModel\Template\Collection $ourTemplateCollection */
            $ourTemplateCollection =
                $this->_objectManager->get('Amasty\OrderStatus\Model\ResourceModel\Template\Collection');
            $ourTemplateId = $ourTemplateCollection->loadTemplateId($ourStatus->getId(), $storeId);
            if ($ourTemplateId != 0) {
                $templateId = $ourTemplateId;
            }
        }

        $this->identityContainer->setCustomerName($customerName);
        $this->identityContainer->setCustomerEmail($order->getCustomerEmail());
        $this->templateContainer->setTemplateId($templateId);
    }
}
