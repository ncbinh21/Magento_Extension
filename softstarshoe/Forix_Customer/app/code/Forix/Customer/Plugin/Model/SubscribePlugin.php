<?php

namespace Forix\Customer\Plugin\Model;

class SubscribePlugin
{
    /**
     * SubscribePlugin constructor.
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     */
    public function __construct(
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
    ) {
        $this->subscriberFactory = $subscriberFactory;
    }

    /**
     * @param \Magento\Catalog\Model\Product $subject
     * @param $result
     * @return mixed
     */
    public function afterSave(\Magento\Customer\Model\ResourceModel\CustomerRepository $subject, $result) {
        if($result->getId()) {
            $this->subscriberFactory->create()->subscribeCustomerById($result->getId());
        }
        return $result;
    }
}