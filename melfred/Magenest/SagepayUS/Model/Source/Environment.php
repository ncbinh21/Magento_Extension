<?php

namespace Magenest\SagepayUS\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class Environment implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'cert',
                'label' => __('certification'),
            ],
            [
                'value' => 'prod',
                'label' => __('production')
            ]
        ];
    }
}
