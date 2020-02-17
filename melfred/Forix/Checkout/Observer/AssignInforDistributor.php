<?php

namespace Forix\Checkout\Observer;

class AssignInforDistributor
{
    /**
     * @var \Forix\Distributor\Model\ZipcodeFactory
     */
    protected $zipCodeFactory;

    /**
     * @var \Amasty\Storelocator\Model\ResourceModel\Location\CollectionFactory
     */
    protected $collectionLocationFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * AssignInforDistributor constructor.
     * @param \Forix\Distributor\Model\ZipcodeFactory $zipcodeFactory
     * @param \Amasty\Storelocator\Model\ResourceModel\Location\CollectionFactory $collectionLocationFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Forix\Distributor\Model\ZipcodeFactory $zipcodeFactory,
        \Amasty\Storelocator\Model\ResourceModel\Location\CollectionFactory $collectionLocationFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->zipcodeFactory = $zipcodeFactory;
        $this->collectionLocationFactory = $collectionLocationFactory;
        $this->logger = $logger;
    }

    public function getNameDistributor($order)
    {
        try {
            if($order && !$order->getSalesOrderNo()) {
                $zipCode = $order->getBillingAddress()->getPostcode();
                $distributorZipCode = $this->zipcodeFactory->create()->getCollection()->addFieldToFilter('zipcode', $zipCode)->getFirstItem();
                $order->setDistributorFullfield(0);
                if ($distributorId = $distributorZipCode->getDistributorId()) {
                    $location = $this->collectionLocationFactory->create()->addFieldToFilter('id', $distributorId)->getFirstItem();
                    if ($location->getName()) {
                        return $location->getName();
//                        $order->setDistributorName($location->getName());
//                        $order->setDistributorFulfilled(1);
                    }
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->critical($e);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException($e->getMessage());
            $this->logger->critical($e);
        }
    }
}