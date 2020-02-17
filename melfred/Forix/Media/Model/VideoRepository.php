<?php


namespace Forix\Media\Model;

use Magento\Framework\Api\SortOrder;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\NoSuchEntityException;
use Forix\Media\Model\ResourceModel\Video as ResourceVideo;
use Forix\Media\Api\Data\VideoInterfaceFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Forix\Media\Api\VideoRepositoryInterface;
use Forix\Media\Api\Data\VideoSearchResultsInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Forix\Media\Model\ResourceModel\Video\CollectionFactory as VideoCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class VideoRepository implements VideoRepositoryInterface
{

    protected $resource;

    protected $videoFactory;

    protected $dataVideoFactory;

    protected $dataObjectHelper;

    private $storeManager;

    protected $videoCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectProcessor;


    /**
     * @param ResourceVideo $resource
     * @param VideoFactory $videoFactory
     * @param VideoInterfaceFactory $dataVideoFactory
     * @param VideoCollectionFactory $videoCollectionFactory
     * @param VideoSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceVideo $resource,
        VideoFactory $videoFactory,
        VideoInterfaceFactory $dataVideoFactory,
        VideoCollectionFactory $videoCollectionFactory,
        VideoSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->videoFactory = $videoFactory;
        $this->videoCollectionFactory = $videoCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataVideoFactory = $dataVideoFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Forix\Media\Api\Data\VideoInterface $video
    ) {
        /* if (empty($video->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $video->setStoreId($storeId);
        } */
        try {
            $this->resource->save($video);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the video: %1',
                $exception->getMessage()
            ));
        }
        return $video;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($videoId)
    {
        $video = $this->videoFactory->create();
        $this->resource->load($video, $videoId);
        if (!$video->getId()) {
            throw new NoSuchEntityException(__('Video with id "%1" does not exist.', $videoId));
        }
        return $video;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->videoCollectionFactory->create();
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
        \Forix\Media\Api\Data\VideoInterface $video
    ) {
        try {
            $this->resource->delete($video);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Video: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($videoId)
    {
        return $this->delete($this->getById($videoId));
    }
}
