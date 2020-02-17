<?php


namespace Forix\ProductWizard\Model;

use Forix\ProductWizard\Model\ResourceModel\Group as ResourceGroup;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Forix\ProductWizard\Api\Data\GroupInterfaceFactory;
use Forix\ProductWizard\Model\ResourceModel\Group\CollectionFactory as GroupCollectionFactory;
use Forix\ProductWizard\Api\GroupRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\DataObjectHelper;
use Forix\ProductWizard\Api\Data\GroupSearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Reflection\DataObjectProcessor;

class GroupRepository implements groupRepositoryInterface
{

    protected $dataGroupFactory;

    protected $resource;

    private $storeManager;

    protected $groupFactory;

    protected $dataObjectProcessor;

    protected $searchResultsFactory;

    protected $groupCollectionFactory;

    protected $dataObjectHelper;


    /**
     * @param ResourceGroup $resource
     * @param GroupFactory $groupFactory
     * @param GroupInterfaceFactory $dataGroupFactory
     * @param GroupCollectionFactory $groupCollectionFactory
     * @param GroupSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceGroup $resource,
        GroupFactory $groupFactory,
        GroupInterfaceFactory $dataGroupFactory,
        GroupCollectionFactory $groupCollectionFactory,
        GroupSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->groupFactory = $groupFactory;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataGroupFactory = $dataGroupFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Forix\ProductWizard\Api\Data\GroupInterface $group
    ) {
        /* if (empty($group->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $group->setStoreId($storeId);
        } */
        try {
            $group->getResource()->save($group);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the group: %1',
                $exception->getMessage()
            ));
        }
        return $group;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($groupId)
    {
        $group = $this->groupFactory->create();
        $group->getResource()->load($group, $groupId);
        if (!$group->getId()) {
            throw new NoSuchEntityException(__('Group with id "%1" does not exist.', $groupId));
        }
        return $group;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->groupCollectionFactory->create();
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
        \Forix\ProductWizard\Api\Data\GroupInterface $group
    ) {
        try {
            $group->getResource()->delete($group);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Group: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($groupId)
    {
        return $this->delete($this->getById($groupId));
    }
}
