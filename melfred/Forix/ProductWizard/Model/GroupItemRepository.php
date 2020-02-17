<?php


namespace Forix\ProductWizard\Model;

use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\NoSuchEntityException;
use Forix\ProductWizard\Api\GroupItemRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Forix\ProductWizard\Api\Data\GroupItemSearchResultsInterfaceFactory;
use Forix\ProductWizard\Api\Data\GroupItemInterfaceFactory;
use Forix\ProductWizard\Model\ResourceModel\GroupItem as ResourceGroupItem;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Forix\ProductWizard\Model\ResourceModel\GroupItem\CollectionFactory as GroupItemCollectionFactory;

class GroupItemRepository implements groupItemRepositoryInterface
{

    protected $groupItemCollectionFactory;

    protected $dataGroupItemFactory;

    protected $resource;

    private $storeManager;

    protected $dataObjectProcessor;

    protected $groupItemFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;


    /**
     * @param ResourceGroupItem $resource
     * @param GroupItemFactory $groupItemFactory
     * @param GroupItemInterfaceFactory $dataGroupItemFactory
     * @param GroupItemCollectionFactory $groupItemCollectionFactory
     * @param GroupItemSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceGroupItem $resource,
        GroupItemFactory $groupItemFactory,
        GroupItemInterfaceFactory $dataGroupItemFactory,
        GroupItemCollectionFactory $groupItemCollectionFactory,
        GroupItemSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->groupItemFactory = $groupItemFactory;
        $this->groupItemCollectionFactory = $groupItemCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataGroupItemFactory = $dataGroupItemFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Forix\ProductWizard\Api\Data\GroupItemInterface $groupItem
    ) {
        /* if (empty($groupItem->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $groupItem->setStoreId($storeId);
        } */
        try {
            $groupItem->getResource()->save($groupItem);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the groupItem: %1',
                $exception->getMessage()
            ));
        }
        return $groupItem;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($groupItemId)
    {
        $groupItem = $this->groupItemFactory->create();
        $groupItem->getResource()->load($groupItem, $groupItemId);
        if (!$groupItem->getId()) {
            throw new NoSuchEntityException(__('Group_Item with id "%1" does not exist.', $groupItemId));
        }
        return $groupItem;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->groupItemCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $fields[] = $filter->getField();
                $condition = $filter->getConditionType() ?: 'eq';
                $conditions[] = [$condition => $filter->getValue()];
            }
            $collection->addFieldToFilter($fields, $conditions);
        }
        
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Forix\ProductWizard\Api\Data\GroupItemInterface $groupItem
    ) {
        try {
            $groupItem->getResource()->delete($groupItem);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Group_Item: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($groupItemId)
    {
        return $this->delete($this->getById($groupItemId));
    }
}
