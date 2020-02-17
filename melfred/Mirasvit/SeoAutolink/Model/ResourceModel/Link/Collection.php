<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoAutolink\Model\ResourceModel\Link;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
    implements \Magento\Framework\Option\ArrayInterface //@codingStandardsIgnoreLine
{
    /**
     * @var string
     */
    protected $_idFieldName = 'link_id'; //use in massaction
    /**
     * Load data for preview flag
     *
     * @var bool
     */
    protected $_previewFlag;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Data\Collection\EntityFactoryInterface
     */
    protected $entityFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Data\Collection\Db\FetchStrategyInterface
     */
    protected $fetchStrategy;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    protected $resource;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory
     * @param \Psr\Log\LoggerInterface                                     $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface               $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb         $resource
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->storeManager = $storeManager;
        $this->entityFactory = $entityFactory;
        $this->logger = $logger;
        $this->fetchStrategy = $fetchStrategy;
        $this->eventManager = $eventManager;
        $this->connection = $connection;
        $this->resource = $resource;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\SeoAutolink\Model\Link', 'Mirasvit\SeoAutolink\Model\ResourceModel\Link');
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $this->addFieldToFilter('is_active', 1)
            ->setOrder('sort_order', 'asc');

        return $this->_toOptionArray('link_id');
    }

    /**
     * @return $this
     */
    public function addActiveFilter()
    {
        $date = new \Zend_Date();
        $activeFrom = [];
        $activeFrom[] = ['date' => true, 'to' => $date->toString('YYYY-MM-dd H:mm:ss')];
        $activeFrom[] = ['date' => true, 'null' => true];

        $activeTo = [];
        $activeTo[] = ['date' => true, 'from' => $date->toString('YYYY-MM-dd H:mm:ss')];
        $activeTo[] = ['date' => true, 'null' => true];

        $this->addFieldToFilter('active_from', $activeFrom)
            ->addFieldToFilter('active_to', $activeTo)
            ->addFieldToFilter('is_active', 1);

        return $this;
    }

    /**
     * @param \Magento\Store\Model\Store|int $store
     * @return $this
     */
    public function addStoreFilter($store)
    {
        if ($store instanceof \Magento\Store\Model\Store) {
            $store = [$store->getId()];
        }

        $this->getSelect()
            ->joinLeft(
                ['store_table' => $this->getTable('mst_seoautolink_link_store')],
                'main_table.link_id = store_table.link_id',
                []
            )
            ->where('store_table.store_id in (?)', [0, $store]);

        return $this;
    }

    /**
     * Set first store flag
     *
     * @param bool $flag
     * @return $this
     */
    public function setFirstStoreFlag($flag = false)
    {
        $this->_previewFlag = $flag;
        return $this;
    }

    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->performAfterLoad('mst_seoautolink_link_store', 'link_id', 'store_id');
        $this->_previewFlag = false;

        return parent::_afterLoad();
    }

    /**
     * Perform operations after collection load
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $columnStoreName
     * @return void
     */
    protected function performAfterLoad($tableName, $columnName, $columnStoreName)
    {
        $items = $this->getColumnValues($columnName);
        if (count($items)) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from(['seoautolink_entity_store' => $this->getTable($tableName)], [$columnName, $columnStoreName])
                ->where('seoautolink_entity_store.' . $columnName . ' IN (?)', $items);
            $result = $connection->fetchPairs($select);
            if ($result) {
                foreach ($this as $item) {
                    $entityId = $item->getData($columnName);
                    if (!isset($result[$entityId])) {
                        continue;
                    }
                    if ($result[$entityId] == 0) {
                        $stores = $this->storeManager->getStores(false, true);
                        $storeId = current($stores)->getId();
                        $storeCode = key($stores);
                    } else {
                        $storeId = $result[$item->getData($columnName)];
                        $storeCode = $this->storeManager->getStore($storeId)->getCode();
                    }
                    $item->setData('_first_store_id', $storeId);
                    $item->setData('store_code', $storeCode);
                    $item->setData('store_id', [$result[$entityId]]);
                }
            }
        }
    }

    /**
     * @return $this
     */
    public function addStoreColumn()
    {
        $this->getSelect()
            ->columns(
                ['store_id' => new \Zend_Db_Expr(
                    "(SELECT GROUP_CONCAT(store_id) FROM `{$this->getTable('mst_seoautolink_link_store')}`
                    AS `seoautolink_link_store_table`
                    WHERE main_table.link_id = seoautolink_link_store_table.link_id)")]
            );

        return $this;
    }

}
