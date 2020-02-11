<?php
/**
 * Copyright © 2016 CommerceExtensions . All rights reserved.
 */
namespace CommerceExtensions\GuestToReg\Model\Config\Source;
 
class CustomerGroup implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    protected $customerGroupCollecton;

    public function __construct(
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroupCollecton
    ) {
       $this->customerGroupCollecton = $customerGroupCollecton;
    }


    public function toOptionArray( $isMultiselect = false)
    {
        return $this->customerGroupCollecton->toOptionArray();
    }
}