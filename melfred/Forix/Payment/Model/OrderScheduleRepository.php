<?php


namespace Forix\Payment\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Forix\Payment\Api\OrderScheduleRepositoryInterface;
use Forix\Payment\Api\Data\OrderScheduleInterfaceFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Store\Model\StoreManagerInterface;
use Forix\Payment\Model\ResourceModel\OrderSchedule as ResourceOrderSchedule;
use Magento\Framework\Exception\CouldNotDeleteException;
use Forix\Payment\Model\ResourceModel\OrderSchedule\CollectionFactory as OrderScheduleCollectionFactory;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Forix\Payment\Api\Data\OrderScheduleSearchResultsInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class OrderScheduleRepository implements OrderScheduleRepositoryInterface
{

    private $collectionProcessor;

    protected $extensibleDataObjectConverter;
    protected $orderScheduleCollectionFactory;

    protected $extensionAttributesJoinProcessor;

    protected $dataObjectHelper;

    protected $dataOrderScheduleFactory;

    protected $orderScheduleFactory;

    protected $resource;

    protected $searchResultsFactory;

    protected $dataObjectProcessor;

    private $storeManager;


    /**
     * @param ResourceOrderSchedule $resource
     * @param OrderScheduleFactory $orderScheduleFactory
     * @param OrderScheduleInterfaceFactory $dataOrderScheduleFactory
     * @param OrderScheduleCollectionFactory $orderScheduleCollectionFactory
     * @param OrderScheduleSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceOrderSchedule $resource,
        OrderScheduleFactory $orderScheduleFactory,
        OrderScheduleInterfaceFactory $dataOrderScheduleFactory,
        OrderScheduleCollectionFactory $orderScheduleCollectionFactory,
        OrderScheduleSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->orderScheduleFactory = $orderScheduleFactory;
        $this->orderScheduleCollectionFactory = $orderScheduleCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataOrderScheduleFactory = $dataOrderScheduleFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Forix\Payment\Api\Data\OrderScheduleInterface $orderSchedule
    ) {
        /* if (empty($orderSchedule->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $orderSchedule->setStoreId($storeId);
        } */
        
        $orderScheduleData = $this->extensibleDataObjectConverter->toNestedArray(
            $orderSchedule,
            [],
            \Forix\Payment\Api\Data\OrderScheduleInterface::class
        );
        
        $orderScheduleModel = $this->orderScheduleFactory->create()->setData($orderScheduleData);
        
        try {
            $this->resource->save($orderScheduleModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the orderSchedule: %1',
                $exception->getMessage()
            ));
        }
        return $orderScheduleModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($orderScheduleId)
    {
        $orderSchedule = $this->orderScheduleFactory->create();
        $this->resource->load($orderSchedule, $orderScheduleId);
        if (!$orderSchedule->getId()) {
            throw new NoSuchEntityException(__('OrderSchedule with id "%1" does not exist.', $orderScheduleId));
        }
        return $orderSchedule->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->orderScheduleCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Forix\Payment\Api\Data\OrderScheduleInterface::class
        );
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Forix\Payment\Api\Data\OrderScheduleInterface $orderSchedule
    ) {
        try {
            $orderScheduleModel = $this->orderScheduleFactory->create();
            $this->resource->load($orderScheduleModel, $orderSchedule->getOrderscheduleId());
            $this->resource->delete($orderScheduleModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the OrderSchedule: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($orderScheduleId)
    {
        return $this->delete($this->getById($orderScheduleId));
    }
}
