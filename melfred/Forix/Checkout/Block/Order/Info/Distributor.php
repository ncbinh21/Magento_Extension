<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2 - EE - Melfredborzall
 * Date: 19/04/2019
 * Time: 10:43
 */

namespace Forix\Checkout\Block\Order\Info;

class Distributor extends \Magento\Sales\Block\Order\View
{
    protected $_zipcodeFactory;
    protected $_locationFactory;
    protected $collectionOrderFactory;
    public function __construct(
        \Forix\Payment\Model\ResourceModel\OrderSchedule\CollectionFactory $collectionOrderFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Forix\Distributor\Model\ZipcodeFactory $zipcodeFactory,
        \Amasty\Storelocator\Model\LocationFactory $locationFactory,
        array $data = []
    )  {
        $this->collectionOrderFactory = $collectionOrderFactory;
        $this->_zipcodeFactory = $zipcodeFactory;
        $this->_locationFactory = $locationFactory;
        parent::__construct($context, $registry, $httpContext, $paymentHelper, $data);
    }

    public function getDistributor()
    {
        $order = $this->getOrder();
        if($order && $order->getDistributorFulfilled() == 1) {
            $zipCode = $order->getBillingAddress()->getPostcode();
            $distributorZipCode = $this->_zipcodeFactory->create()->load($zipCode,'zipcode');
//            $order->setDistributorFullfield(0);
            if ($distributorId = $distributorZipCode->getDistributorId()) {
                $location = $this->_locationFactory->create()->load($distributorId);
                if($location->getId()) {
                    if ($location->getName()) {
                        $order->setDistributor($location);
                    }
                    return $location;
                }
            }
        }
        return null;
    }

    public function checkPushSageError()
    {
        $order = $this->getOrder();
        $checkPushSage = $this->collectionOrderFactory->create()->addFieldToFilter('parent_id', $order->getIncrementId())->setOrder('orderschedule_id', 'DESC')->getFirstItem();
        if($checkPushSage && $checkPushSage->getId() && !$checkPushSage->getStatus()) {
            return true;
        }
        return false;
    }
}