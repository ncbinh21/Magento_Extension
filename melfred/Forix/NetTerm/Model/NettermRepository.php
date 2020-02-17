<?php


namespace Forix\NetTerm\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Forix\NetTerm\Model\ResourceModel\Netterm as ResourceNetterm;
use Forix\NetTerm\Model\ResourceModel\Netterm\CollectionFactory as NettermCollectionFactory;
use Forix\NetTerm\Api\Data\NettermSearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;
use Forix\NetTerm\Api\NettermRepositoryInterface;
use Forix\NetTerm\Api\Data\NettermInterfaceFactory;
use Magento\Framework\Exception\CouldNotSaveException;

class NettermRepository implements NettermRepositoryInterface
{

    protected $nettermCollectionFactory;

    protected $dataNettermFactory;

    protected $dataObjectHelper;

    protected $nettermFactory;

    protected $resource;

    protected $searchResultsFactory;

    protected $dataObjectProcessor;

    private $storeManager;

    /**
     * @param ResourceNetterm $resource
     * @param NettermFactory $nettermFactory
     * @param NettermInterfaceFactory $dataNettermFactory
     * @param NettermCollectionFactory $nettermCollectionFactory
     * @param NettermSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceNetterm $resource,
        NettermFactory $nettermFactory,
        NettermInterfaceFactory $dataNettermFactory,
        NettermCollectionFactory $nettermCollectionFactory,
        NettermSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->nettermFactory = $nettermFactory;
        $this->nettermCollectionFactory = $nettermCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataNettermFactory = $dataNettermFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Forix\NetTerm\Api\Data\NettermInterface $netterm
    ) {
        /* if (empty($netterm->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $netterm->setStoreId($storeId);
        } */
        try {
            $this->resource->save($netterm);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the Net Terms: %1',
                $exception->getMessage()
            ));
        }
        return $netterm;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($nettermId)
    {
        $netterm = $this->nettermFactory->create();
        $this->resource->load($netterm, $nettermId);
        if (!$netterm->getId()) {
            throw new NoSuchEntityException(__('Net Terms with id "%1" does not exist.', $nettermId));
        }
        return $netterm;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->nettermCollectionFactory->create();
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
        \Forix\NetTerm\Api\Data\NettermInterface $netterm
    ) {
        try {
            $this->resource->delete($netterm);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Net Terms: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($nettermId)
    {
        return $this->delete($this->getById($nettermId));
    }
}
