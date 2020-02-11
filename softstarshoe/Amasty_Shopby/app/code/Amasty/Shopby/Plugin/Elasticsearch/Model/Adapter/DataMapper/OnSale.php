<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Plugin\Elasticsearch\Model\Adapter\DataMapper;

use Amasty\Shopby\Plugin\Elasticsearch\Model\Adapter\DataMapperInterface;
use Magento\Customer\Model\ResourceModel\Group\Collection;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory;

/**
 * Class OnSale
 * @package Amasty\Shopby\Plugin\Elasticsearch\Model\Adapter\DataMapper
 */
class OnSale implements DataMapperInterface
{
    const FIELD_NAME = 'on_sale';

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var \Amasty\Shopby\Model\Layer\Filter\OnSale\Helper
     */
    private $onSaleHelper;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    private $customerGrouprCollectionFactory;

    /**
     * @var array
     */
    private $onSaleProductIds = [];

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * OnSale constructor.
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $customerGroupCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Amasty\Shopby\Model\Layer\Filter\OnSale\Helper $onSaleHelper
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $customerGroupCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\Shopby\Model\Layer\Filter\OnSale\Helper $onSaleHelper
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->customerGrouprCollectionFactory = $customerGroupCollectionFactory;
        $this->storeManager = $storeManager;
        $this->onSaleHelper = $onSaleHelper;
    }

    /**
     * @param int $entityId
     * @param array $entityIndexData
     * @param int $storeId
     * @param array $context
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function map($entityId, array $entityIndexData, $storeId, $context = [])
    {
        $collection = $this->customerGrouprCollectionFactory->create();
        $mappedData = [];
        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
        foreach ($collection as $item) {
            $mappedData[self::FIELD_NAME . '_' . $item->getId() . '_' . $websiteId] =
                    (int)$this->isProductOnSale($entityId, $storeId, $item->getid());
        }
        return $mappedData;
    }

    /**
     * @param int $entityId
     * @param int $storeId
     * @param itn $groupId
     * @return bool
     */
    private function isProductOnSale($entityId, $storeId, $groupId)
    {
        if (isset($this->getOnSaleProductIds($storeId)[$entityId])) {
            $groupIds = $this->getOnSaleProductIds($storeId)[$entityId];
            return empty($groupIds) || in_array($groupId, $groupIds);
        }
        return false;
    }

    /**
     * @return array
     */
    private function getOnSaleProductIds($storeId)
    {
        if (!isset($this->onSaleProductIds[$storeId]) || empty($this->onSaleProductIds[$storeId])) {
            $this->onSaleProductIds[$storeId] = [];
            $collection = $this->productCollectionFactory->create()->addStoreFilter($storeId);
            $this->onSaleHelper->addOnSaleFilter($collection, $storeId, false);
            $collection->getSelect()->group('e.entity_id');
            $collection->getSelect()->columns(
                ['customer_group_ids' =>
                    new \Zend_Db_Expr('GROUP_CONCAT(catalog_rule.customer_group_id SEPARATOR ",")')]
            );
            foreach ($collection as $item) {
                $groupIds = $item->getCustomerGroupIds() === null ?
                    [] : array_unique(explode(',', $item->getCustomerGroupIds()));
                $this->onSaleProductIds[$storeId][$item->getId()] = $groupIds;
            }
        }
        return $this->onSaleProductIds[$storeId];
    }
}
