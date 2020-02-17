<?php


namespace Forix\Distributor\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Forix\Distributor\Model\ResourceModel\Zipcode\CollectionFactory as ZipcodeCollectionFactory;
use Forix\Distributor\Api\Data\ZipcodeSearchResultsInterfaceFactory;
use Forix\Distributor\Api\Data\ZipcodeInterfaceFactory;
use Magento\Framework\Api\SortOrder;
use Forix\Distributor\Model\ResourceModel\Zipcode as ResourceZipcode;
use Forix\Distributor\Api\ZipcodeRepositoryInterface;

class ZipcodeRepository implements ZipcodeRepositoryInterface
{

    private $storeManager;
    protected $zipcodeCollectionFactory;

    protected $resource;

    protected $zipcodeFactory;

    protected $searchResultsFactory;

    protected $dataZipcodeFactory;

    protected $dataObjectProcessor;

    protected $dataObjectHelper;


    /**
     * @param ResourceZipcode $resource
     * @param ZipcodeFactory $zipcodeFactory
     * @param ZipcodeInterfaceFactory $dataZipcodeFactory
     * @param ZipcodeCollectionFactory $zipcodeCollectionFactory
     * @param ZipcodeSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceZipcode $resource,
        ZipcodeFactory $zipcodeFactory,
        ZipcodeInterfaceFactory $dataZipcodeFactory,
        ZipcodeCollectionFactory $zipcodeCollectionFactory,
        ZipcodeSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->zipcodeFactory = $zipcodeFactory;
        $this->zipcodeCollectionFactory = $zipcodeCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataZipcodeFactory = $dataZipcodeFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Forix\Distributor\Api\Data\ZipcodeInterface $zipcode
    ) {
        /* if (empty($zipcode->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $zipcode->setStoreId($storeId);
        } */
        try {
            $this->resource->save($zipcode);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the zipcode: %1',
                $exception->getMessage()
            ));
        }
        return $zipcode;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($zipcodeId)
    {
        $zipcode = $this->zipcodeFactory->create();
        $this->resource->load($zipcode, $zipcodeId);
        if (!$zipcode->getId()) {
            throw new NoSuchEntityException(__('zipcode with id "%1" does not exist.', $zipcodeId));
        }
        return $zipcode;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        /** @var \Forix\Distributor\Model\ResourceModel\Zipcode\Collection $collection */
        $collection = $this->zipcodeCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    continue;
                }
                $fields[] = $filter->getField();
                $condition = $filter->getConditionType() ?: 'eq';
                $conditions[] = [$condition => $filter->getValue()];
            }
            $collection->addFieldToFilter($fields, $conditions);
        }
        $collection->join(['aml' => $collection->getTable('amasty_amlocator_location')],'aml.id = main_table.distributor_id',[])
            ->join(['cd' => $collection->getTable('company_distributors')],'cd.distributor_id = main_table.distributor_id',[])
            ->addFieldToFilter('aml.status',['eq'=>1]);
        $collection->getSelect()->group('main_table.zipcode_id');
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
        \Forix\Distributor\Api\Data\ZipcodeInterface $zipcode
    ) {
        try {
            $this->resource->delete($zipcode);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the zipcode: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($zipcodeId)
    {
        return $this->delete($this->getById($zipcodeId));
    }
}
