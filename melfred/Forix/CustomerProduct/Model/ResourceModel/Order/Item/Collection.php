<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 */

namespace Forix\CustomerProduct\Model\ResourceModel\Order\Item;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;

class Collection extends \Magento\Sales\Model\ResourceModel\Order\Item\Collection
{
    /**
     * Collection constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param Snapshot $entitySnapshot
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager, Snapshot $entitySnapshot,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $entitySnapshot, $connection, $resource);
    }

    /**
     * @return $this
     */
    protected function _initSelect()
    {

        parent::_initSelect();
        $this->getSelect()->joinInner(
            ['order' => $this->getTable('sales_order')],
            "main_table.order_id = order.entity_id",
            ["customer_id", "increment_id", 'created_order'=>'created_at']
        )->where('main_table.parent_item_id IS NULL');

        return $this;
    }

    /**
     * @param $customerId
     * @return $this
     */
    public function addCustomerToFilter($customerId)
    {
        $this->getSelect()->where(new \Zend_Db_Expr("order.customer_id = {$customerId}"));
        return $this;
    }

    /**
     * @return $this
     * get all category
     */
    public function getAllCategory()
    {
        $storeId = $this->storeManager->getStore()->getId();
        $nameCategoryFlat = 'catalog_category_flat_store_' . $storeId;
        $this->getSelect()->joinLeft(['category' => $this->getTable('catalog_category_product')],
            "main_table.product_id = category.product_id",
            ["cate_id" =>"category_id"]
        )->joinLeft(['category_flat' => $this->getTable($nameCategoryFlat)],
            "category.category_id = category_flat.entity_id",
            ["cate_name" => "GROUP_CONCAT(category_flat.name)"])->where("category_flat.level >= 2")->group('main_table.item_id');
        return $this;
    }

    /**
     * @return $this
     * get list category id
     */
    public function getListCategoryId()
    {
        $storeId = $this->storeManager->getStore()->getId();
        $nameCategoryFlat = 'catalog_category_flat_store_' . $storeId;
        $this->getSelect()->joinLeft(['category' => $this->getTable('catalog_category_product')],
            "main_table.product_id = category.product_id",
            ["cate_id" =>"GROUP_CONCAT(category_id)"]
        )->joinLeft(['category_flat' => $this->getTable($nameCategoryFlat)],
            "category.category_id = category_flat.entity_id",
            [])->where("category_flat.level >= 2")->group('main_table.item_id');
        return $this;
    }

    /**
     * @param $paramCategory
     * @return $this
     * filter by category
     */
    public function filterByCategory($paramCategory)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $nameCategoryFlat = 'catalog_category_flat_store_' . $storeId;
        $this->getSelect()->joinLeft(['category' => $this->getTable('catalog_category_product')],
            "main_table.product_id = category.product_id",
            ["cate_id" =>"category_id"]
        )->joinLeft(['category_flat' => $this->getTable($nameCategoryFlat)],
            "category.category_id = category_flat.entity_id",
            ["cate_name" => "category_flat.name"])->where("category_flat.level >= 2")->where('category.category_id = ?' , $paramCategory);
        return $this;
    }
}