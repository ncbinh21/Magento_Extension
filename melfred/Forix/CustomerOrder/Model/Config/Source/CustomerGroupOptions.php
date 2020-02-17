<?php

namespace Forix\CustomerOrder\Model\Config\Source;
/**
 * Class CustomerGroupOptions
 * @package Forix\CustomerOrder\Model\Config\Source
 */
class CustomerGroupOptions implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    protected $_customerGroupFactory;

    /**
     * CustomerGroupOptions constructor.
     * @param \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $customerGroupCollectionFactory
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $customerGroupCollectionFactory
    )
    {
        $this->_customerGroupFactory = $customerGroupCollectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = $this->_customerGroupFactory->create()->load()->toOptionArray();
        return $options;
    }

}
