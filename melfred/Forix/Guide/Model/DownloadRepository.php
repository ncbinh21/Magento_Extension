<?php


namespace Forix\Guide\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotDeleteException;
use Forix\Guide\Api\Data\DownloadInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Forix\Guide\Api\Data\DownloadSearchResultsInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Forix\Guide\Model\ResourceModel\Download\CollectionFactory as DownloadCollectionFactory;
use Forix\Guide\Api\DownloadRepositoryInterface;
use Forix\Guide\Model\ResourceModel\Download as ResourceDownload;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;

class DownloadRepository implements DownloadRepositoryInterface
{

    private $storeManager;
    protected $downloadCollectionFactory;

    protected $resource;

    protected $dataDownloadFactory;

    protected $searchResultsFactory;

    protected $dataObjectProcessor;

    protected $downloadFactory;

    protected $dataObjectHelper;


    /**
     * @param ResourceDownload $resource
     * @param DownloadFactory $downloadFactory
     * @param DownloadInterfaceFactory $dataDownloadFactory
     * @param DownloadCollectionFactory $downloadCollectionFactory
     * @param DownloadSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceDownload $resource,
        DownloadFactory $downloadFactory,
        DownloadInterfaceFactory $dataDownloadFactory,
        DownloadCollectionFactory $downloadCollectionFactory,
        DownloadSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->downloadFactory = $downloadFactory;
        $this->downloadCollectionFactory = $downloadCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataDownloadFactory = $dataDownloadFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Forix\Guide\Api\Data\DownloadInterface $download
    ) {
        /* if (empty($download->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $download->setStoreId($storeId);
        } */
        try {
            $this->resource->save($download);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the download: %1',
                $exception->getMessage()
            ));
        }
        return $download;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($downloadId)
    {
        $download = $this->downloadFactory->create();
        $this->resource->load($download, $downloadId);
        if (!$download->getId()) {
            throw new NoSuchEntityException(__('download with id "%1" does not exist.', $downloadId));
        }
        return $download;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->downloadCollectionFactory->create();
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
        \Forix\Guide\Api\Data\DownloadInterface $download
    ) {
        try {
            $this->resource->delete($download);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the download: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($downloadId)
    {
        return $this->delete($this->getById($downloadId));
    }
}
