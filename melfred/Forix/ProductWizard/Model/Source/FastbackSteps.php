<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 05/06/2018
 * Time: 17:25
 */
namespace Forix\ProductWizard\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
class FastbackSteps implements OptionSourceInterface
{

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 1,
                'label' => __('Step 1')
            ],
            [
                'value' => 2,
                'label' => __('Step 2')
            ],
            [
                'value' => 3,
                'label' => __('Step 3')
            ]
        ];
    }
}