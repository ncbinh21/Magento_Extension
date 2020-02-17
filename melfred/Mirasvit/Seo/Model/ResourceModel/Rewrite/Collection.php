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



namespace Mirasvit\Seo\Model\ResourceModel\Rewrite;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'rewrite_id'; //use in massaction

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
        $this->_init('Mirasvit\Seo\Model\Rewrite', 'Mirasvit\Seo\Model\ResourceModel\Rewrite');
    }

    /**
     * Add Filter by store.
     *
     * @param int|\Magento\Store\Model\Store $store
     *
     * @return \My\rewrite\Model\Mysql4\News\Collection
     */
    public function addStoreFilter($store)
    {
        //        if (!$this->context->getStoreManager()->isSingleStoreMode()) {
        if ($store instanceof \Magento\Store\Model\Store) {
            $store = [$store->getId()];
        }

        $this->getSelect()
                ->join(
                    ['store_table' => $this->getTable('mst_seo_rewrite_store')],
                    'main_table.rewrite_id = store_table.rewrite_id',
                    []
                )
                ->where('store_table.store_id in (?)', [0, $store]);

        return $this;
    }

    /**
     * Add Filter by status.
     *
     * @param int $status
     *
     * @return \My\rewrite\Model\Mysql4\News\Collection
     */
    public function addEnableFilter($status = 1)
    {
        $this->getSelect()->where('main_table.is_active = ?', $status);

        return $this;
    }

    /**
     * @return $this
     */
    public function addStoreColumn()
    {
        $this->getSelect()
            ->columns(
                ['store_id' => new \Zend_Db_Expr(
                    "(SELECT GROUP_CONCAT(store_id) FROM `{$this->getTable('mst_seo_rewrite_store')}`
                    AS `seo_rewrite_store_table`
                    WHERE main_table.rewrite_id = seo_rewrite_store_table.rewrite_id)")]
            );

        return $this;
    }
}
