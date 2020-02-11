<?php


namespace Forix\FanPhoto\Model;

use Forix\FanPhoto\Api\PhotoRepositoryInterface;
use Forix\FanPhoto\Api\Data\PhotoSearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Forix\FanPhoto\Api\Data\PhotoInterfaceFactory;
use Magento\Framework\Api\SortOrder;
use Forix\FanPhoto\Model\ResourceModel\Photo as ResourcePhoto;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\DataObjectHelper;
use Forix\FanPhoto\Model\ResourceModel\Photo\CollectionFactory as PhotoCollectionFactory;

class PhotoRepository implements photoRepositoryInterface
{

    protected $dataObjectProcessor;

    protected $dataObjectHelper;

    protected $searchResultsFactory;

    protected $photoFactory;

    protected $dataPhotoFactory;

    protected $photoCollectionFactory;

    protected $resource;

    private $storeManager;


    /**
     * @param ResourcePhoto $resource
     * @param PhotoFactory $photoFactory
     * @param PhotoInterfaceFactory $dataPhotoFactory
     * @param PhotoCollectionFactory $photoCollectionFactory
     * @param PhotoSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourcePhoto $resource,
        PhotoFactory $photoFactory,
        PhotoInterfaceFactory $dataPhotoFactory,
        PhotoCollectionFactory $photoCollectionFactory,
        PhotoSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->photoFactory = $photoFactory;
        $this->photoCollectionFactory = $photoCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPhotoFactory = $dataPhotoFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Forix\FanPhoto\Api\Data\PhotoInterface $photo
    ) {
        /* if (empty($photo->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $photo->setStoreId($storeId);
        } */
        try {
            $photo->getResource()->save($photo);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the photo: %1',
                $exception->getMessage()
            ));
        }
        return $photo;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($photoId)
    {
        $photo = $this->photoFactory->create();
        $photo->getResource()->load($photo, $photoId);
        if (!$photo->getId()) {
            throw new NoSuchEntityException(__('Photo with id "%1" does not exist.', $photoId));
        }
        return $photo;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->photoCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
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
        \Forix\FanPhoto\Api\Data\PhotoInterface $photo
    ) {
        try {
            $photo->getResource()->delete($photo);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Photo: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($photoId)
    {
        return $this->delete($this->getById($photoId));
    }
}
