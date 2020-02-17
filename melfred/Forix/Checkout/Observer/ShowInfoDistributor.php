<?php

namespace Forix\Checkout\Observer;

use Magento\Framework\Event\ObserverInterface;

class ShowInfoDistributor implements ObserverInterface
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
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Region\CollectionFactory
     */
    protected $collectionRegion;

    /**
     * ShowInfoDistributor constructor.
     * @param \Forix\Distributor\Model\ZipcodeFactory $zipCodeFactory
     * @param \Amasty\Storelocator\Model\ResourceModel\Location\CollectionFactory $collectionLocationFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $collectionRegion
     */
    public function __construct(
        \Forix\Distributor\Model\ZipcodeFactory $zipCodeFactory,
        \Amasty\Storelocator\Model\ResourceModel\Location\CollectionFactory $collectionLocationFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $collectionRegion
    ) {
        $this->zipCodeFactory = $zipCodeFactory;
        $this->collectionLocationFactory = $collectionLocationFactory;
        $this->registry = $registry;
        $this->collectionRegion = $collectionRegion;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $transportArray = $observer->getTransport();
        $order = $observer->getTransport()->getOrder();
        if($order && !$order->getSalesOrderNo()) {
            $this->registry->unregister('forix_order');
            $this->registry->register('forix_order', $order);
            if ($zipCode = $order->getBillingAddress()->getPostcode()) {
                $distributorZipCode = $this->zipCodeFactory->create()->getCollection()->addFieldToFilter('zipcode', $zipCode)->getFirstItem();
                if ($distributorId = $distributorZipCode->getDistributorId()) {
                    $location = $this->collectionLocationFactory->create()->addFieldToFilter('id', $distributorId)->getFirstItem();
                    if ($location) {
                        if ($location->getPhone()) {
                            $location->setData('has_phone', true);
                        }
                        if ($location->getOfficePhone()) {
                            $location->setData('has_office_phone', true);
                        }
                        if ($location->getContact()) {
                            $location->setData('has_contact', true);
                        }
                        $location->setData('code_region', $this->getCodeRegion($location));
                        $observer->getTransport()->setData('distributor', $location);
                        return $this;
                    }
                }
            }
        }
    }

    /**
     * @param $distributor
     * @return mixed
     */
    public function getCodeRegion($distributor)
    {
        $regionCollection = $this->collectionRegion->create();
        $region = $regionCollection->addFieldToFilter('name', ['eq' => $distributor->getState()])
            ->addFieldToFilter('country_id', ['eq' => $distributor->getCountry()])
            ->getFirstItem();
        if($region->getCode()) {
            return $region->getCode();
        }
        return $distributor->getState();
    }
}