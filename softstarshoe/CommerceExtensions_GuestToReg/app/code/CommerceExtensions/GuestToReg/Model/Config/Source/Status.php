<?php
/**
 * Copyright © 2016 CommerceExtensions . All rights reserved.
 */
namespace CommerceExtensions\GuestToReg\Model\Config\Source;
 
class Status implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('No')],
            ['value' => 1, 'label' => __('Yes')],
        ];
    }
}