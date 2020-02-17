<?php

namespace Forix\Checkout\Model\Config\Source;

class Tab implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $data = [];
        $data[] = [
            'label' => __('Horizontal tabs'),
            'value' => '1'
        ];
        $data[] = [
            'label' => __('Radio (by default Magento)'),
            'value' => '0'
        ];
        return $data;
    }
}
