<?php
/**
 * Created by PhpStorm.
 * User: magenest
 * Date: 05/03/2017
 * Time: 16:08
 */

namespace Magenest\SagepayUS\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class PaymentMode implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'custom',
                'label' => __('Magento Custom UI'),
            ],
            [
                'value' => 'modal',
                'label' => __('Sagepay Modal UI')
            ],
            [
                'value' => 'inline',
                'label' => __('Sagepay Inline UI')
            ]
        ];
    }
}
