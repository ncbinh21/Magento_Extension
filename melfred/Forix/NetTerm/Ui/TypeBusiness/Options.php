<?php

namespace Forix\NetTerm\Ui\TypeBusiness;

class Options implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'label' => 'Corporation',
                'value' => 'Corporation'
            ],
            [
                'label' => 'Partnership',
                'value' => 'Partnership'
            ],
            [
                'label' => 'Proprietorship',
                'value' => 'Proprietorship'
            ],
        ];
        return $options;
    }
}
