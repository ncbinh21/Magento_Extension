<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model;

use Aheadworks\Giftcard\Api\PoolRepositoryInterface;
use Aheadworks\Giftcard\Api\Data\PoolInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\EntityManager\EntityManager;
use Aheadworks\Giftcard\Model\Pool as PoolModel;
use Aheadworks\Giftcard\Api\Data\PoolInterfaceFactory;
use Aheadworks\Giftcard\Api\Data\PoolSearchResultsInterface;
use Aheadworks\Giftcard\Api\Data\PoolSearchResultsInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class PoolRepository
 *
 * @package Aheadworks\Giftcard\Model
 */
class PoolRepository implements PoolRepositoryInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var PoolFactory
     */
    private $poolFactory;

    /**
     * @var PoolInterfaceFactory
     */
    private $poolDataFactory;

    /**
     * @var PoolSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * @param EntityManager $entityManager
     * @param PoolFactory $poolFactory
     * @param PoolInterfaceFactory $poolDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param PoolSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        EntityManager $entityManager,
        PoolFactory $poolFactory,
        PoolInterfaceFactory $poolDataFactory,
        DataObjectHelper $dataObjectHelper,
        PoolSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->entityManager = $entityManager;
        $this->poolFactory = $poolFactory;
        $this->poolDataFactory = $poolDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function save(PoolInterface $pool)
    {
        $this->entityManager->save($pool);
        $this->registry[$pool->getId()] = $pool;
        return $pool;
    }

    /**
     * {@inheritdoc}
     */
    public function get($poolId)
    {
        if (!isset($this->registry[$poolId])) {
            /** @var PoolInterface $pool */
            $pool = $this->poolDataFactory->create();
            $this->entityManager->load($pool, $poolId);
            if (!$pool->getId()) {
                throw NoSuchEntityException::singleField('poolId', $poolId);
            }
            $this->registry[$poolId] = $pool;
        }
        return $this->registry[$poolId];
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var PoolSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create()
            ->setSearchCriteria($searchCriteria);
        /** @var \Aheadworks\Giftcard\Model\ResourceModel\Pool\Collection $collection */
        $collection = $this->poolFactory->create()->getCollection();
        $this->extensionAttributesJoinProcessor->process($collection, PoolInterface::class);
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $fields[] = $filter->getField();
                $conditions[] = [$condition => $filter->getValue()];
            }
            if ($fields) {
                $collection->addFieldToFilter($fields, $conditions);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        if ($sortOrders = $searchCriteria->getSortOrders()) {
            /** @var \Magento\Framework\Api\SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder($sortOrder->getField(), $sortOrder->getDirection());
            }
        }

        $collection
            ->setCurPage($searchCriteria->getCurrentPage())
            ->setPageSize($searchCriteria->getPageSize());

        $pools = [];
        /** @var PoolModel $poolModel */
        foreach ($collection as $poolModel) {
            $pools[] = $this->getPoolDataObject($poolModel);
        }
        $searchResults->setItems($pools);
        return $searchResults;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(PoolInterface $pool)
    {
        return $this->deleteById($pool->getId());
    }

    /**
     * {@inheritDoc}
     */
    public function deleteById($poolId)
    {
        $pool = $this->get($poolId);
        $this->entityManager->delete($pool);
        if (isset($this->registry[$poolId])) {
            unset($this->registry[$poolId]);
        }
        return true;
    }

    /**
     * Retrieves pool data object using pool model
     *
     * @param PoolModel $pool
     * @return PoolInterface
     */
    private function getPoolDataObject(PoolModel $pool)
    {
        /** @var PoolInterface $poolDataObject */
        $poolDataObject = $this->poolDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $poolDataObject,
            $pool->getData(),
            PoolInterface::class
        );
        return $poolDataObject;
    }
}
