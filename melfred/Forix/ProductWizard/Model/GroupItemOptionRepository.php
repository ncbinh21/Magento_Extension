<?php


namespace Forix\ProductWizard\Model;

use Forix\ProductWizard\Api\Data\GroupItemOptionSearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Forix\ProductWizard\Model\ResourceModel\GroupItemOption as ResourceGroupItemOption;
use Magento\Framework\Exception\CouldNotSaveException;
use Forix\ProductWizard\Api\GroupItemOptionRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Forix\ProductWizard\Api\Data\GroupItemOptionInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Forix\ProductWizard\Model\ResourceModel\GroupItemOption\CollectionFactory as GroupItemOptionCollectionFactory;

class GroupItemOptionRepository implements groupItemOptionRepositoryInterface
{

    protected $groupItemOptionFactory;

    protected $groupItemOptionCollectionFactory;

    protected $resource;

    private $storeManager;

    protected $dataObjectProcessor;

    protected $dataGroupItemOptionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;


    /**
     * @param ResourceGroupItemOption $resource
     * @param GroupItemOptionFactory $groupItemOptionFactory
     * @param GroupItemOptionInterfaceFactory $dataGroupItemOptionFactory
     * @param GroupItemOptionCollectionFactory $groupItemOptionCollectionFactory
     * @param GroupItemOptionSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceGroupItemOption $resource,
        GroupItemOptionFactory $groupItemOptionFactory,
        GroupItemOptionInterfaceFactory $dataGroupItemOptionFactory,
        GroupItemOptionCollectionFactory $groupItemOptionCollectionFactory,
        GroupItemOptionSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->groupItemOptionFactory = $groupItemOptionFactory;
        $this->groupItemOptionCollectionFactory = $groupItemOptionCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataGroupItemOptionFactory = $dataGroupItemOptionFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Forix\ProductWizard\Api\Data\GroupItemOptionInterface $groupItemOption
    ) {
        /* if (empty($groupItemOption->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $groupItemOption->setStoreId($storeId);
        } */
        try {
            $groupItemOption->getResource()->save($groupItemOption);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the groupItemOption: %1',
                $exception->getMessage()
            ));
        }
        return $groupItemOption;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($groupItemOptionId)
    {
        $groupItemOption = $this->groupItemOptionFactory->create();
        $groupItemOption->getResource()->load($groupItemOption, $groupItemOptionId);
        if (!$groupItemOption->getId()) {
            throw new NoSuchEntityException(__('Group_Item_Option with id "%1" does not exist.', $groupItemOptionId));
        }
        return $groupItemOption;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->groupItemOptionCollectionFactory->create();
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
        \Forix\ProductWizard\Api\Data\GroupItemOptionInterface $groupItemOption
    ) {
        try {
            $groupItemOption->getResource()->delete($groupItemOption);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Group_Item_Option: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($groupItemOptionId)
    {
        return $this->delete($this->getById($groupItemOptionId));
    }
}
