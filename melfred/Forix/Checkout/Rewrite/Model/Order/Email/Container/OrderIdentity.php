<?php

namespace Forix\Checkout\Rewrite\Model\Order\Email\Container;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class OrderIdentity extends \Magento\Sales\Model\Order\Email\Container\OrderIdentity
{
    const XML_PATH_EMAIL_CUSTOMER_DISTRIBUTOR = 'forix_custom_checkout/distributor/email_customer';
    const XML_PATH_EMAIL_GUEST_DISTRIBUTOR = 'forix_custom_checkout/distributor/email_guest';

    /**
     * @var \Forix\Checkout\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Amasty\Storelocator\Model\ResourceModel\Location\CollectionFactory
     */
    protected $collectionLocationFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    public function __construct(
        \Forix\Checkout\Helper\Data $helperData,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        \Amasty\Storelocator\Model\ResourceModel\Location\CollectionFactory $collectionLocationFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->helperData = $helperData;
        $this->collectionLocationFactory = $collectionLocationFactory;
        $this->registry = $registry;
        parent::__construct($scopeConfig, $storeManager);
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function checkDistributor()
    {
        $order = $this->registry->registry('forix_order');
        if($order && !$this->helperData->checkDistributorOrNotZone($order)) {
            return true;
        }
        return false;
    }
    /**
     * Return guest template id
     *
     * @return mixed
     */
    public function getGuestTemplateId()
    {
        if($this->checkDistributor()){
            return $this->getConfigValue(self::XML_PATH_EMAIL_GUEST_DISTRIBUTOR, $this->getStore()->getStoreId());
        }
        return $this->getConfigValue(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $this->getStore()->getStoreId());
    }

    /**
     * Return template id
     *
     * @return mixed
     */
    public function getTemplateId()
    {
        if($this->checkDistributor()){
            return $this->getConfigValue(self::XML_PATH_EMAIL_CUSTOMER_DISTRIBUTOR, $this->getStore()->getStoreId());
        }
        return $this->getConfigValue(self::XML_PATH_EMAIL_TEMPLATE, $this->getStore()->getStoreId());
    }
}