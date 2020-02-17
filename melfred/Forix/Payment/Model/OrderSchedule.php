<?php


namespace Forix\Payment\Model;

use Forix\Payment\Api\Data\OrderScheduleInterface;
use Forix\Payment\Api\Data\OrderScheduleInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class OrderSchedule extends \Magento\Framework\Model\AbstractModel
{

    protected $_eventPrefix = 'forix_payment_orderschedule';
    protected $dataObjectHelper;

    protected $orderscheduleDataFactory;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param OrderScheduleInterfaceFactory $orderscheduleDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Forix\Payment\Model\ResourceModel\OrderSchedule $resource
     * @param \Forix\Payment\Model\ResourceModel\OrderSchedule\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        OrderScheduleInterfaceFactory $orderscheduleDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Forix\Payment\Model\ResourceModel\OrderSchedule $resource,
        \Forix\Payment\Model\ResourceModel\OrderSchedule\Collection $resourceCollection,
        array $data = []
    ) {
        $this->orderscheduleDataFactory = $orderscheduleDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve orderschedule model with orderschedule data
     * @return OrderScheduleInterface
     */
    public function getDataModel()
    {
        $orderscheduleData = $this->getData();
        
        $orderscheduleDataObject = $this->orderscheduleDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $orderscheduleDataObject,
            $orderscheduleData,
            OrderScheduleInterface::class
        );
        
        return $orderscheduleDataObject;
    }
}
