<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Aheadworks\Giftcard\Model\ResourceModel\Statistics as ResourceStatistics;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class Statistics
 *
 * @package Aheadworks\Giftcard\Model
 */
class Statistics extends AbstractModel
{
    /**
     * @var StatisticsFactory
     */
    private $statisticsFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param StatisticsFactory $statisticsFactory
     * @param AbstractResource $resource
     * @param AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        StatisticsFactory $statisticsFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->statisticsFactory = $statisticsFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceStatistics::class);
    }

    /**
     * Create statistics for product
     *
     * @param int $productId
     * @param int $storeId
     * @return bool|$this
     */
    public function createStatistics($productId, $storeId)
    {
        return $this->createNewRowIfNotExists($productId, $storeId);
    }

    /**
     * Update statistics data
     *
     * @param int $productId
     * @param int $storeId
     * @param [] $data
     * @return void
     */
    public function updateStatistics($productId, $storeId, $data)
    {
        /** @var Statistics $statistics */
        if (!$statistics = $this->createNewRowIfNotExists($productId, $storeId)) {
            $statistics = $this->loadByProductAndStore($productId, $storeId);
        }
        foreach ($data as $key => $value) {
            $newValue = $statistics->getData($key) + $value;
            if ($newValue < 0) {
                $newValue = 0;
            }
            $statistics->setData($key, $newValue);
        }
        $this->getResource()->save($statistics);
    }

    /**
     * Create new row if not exists
     *
     * @param int $productId
     * @param int $storeId
     * @return $this|bool
     */
    private function createNewRowIfNotExists($productId, $storeId)
    {
        $statistics = false;
        if (!$this->getResource()->existsStatistics($productId, $storeId)) {
            $statistics = $this->statisticsFactory->create()
                ->setData(
                    [
                        'product_id' => $productId,
                        'store_id' => $storeId
                    ]
                );
            $this->getResource()->save($statistics);
        }
        return $statistics;
    }

    /**
     * Load product by $productId and $storeId
     *
     * @param int $productId
     * @param int $storeId
     * @return $this
     */
    private function loadByProductAndStore($productId, $storeId)
    {
        $statistics = $this->statisticsFactory->create();
        $this->getResource()
            ->setStoreId($storeId)
            ->load($statistics, $productId, 'product_id');
        return $statistics;
    }
}
