<?php

namespace Forix\CategoryCustom\Model\Category;
/**
 * Class BitReamer
 * @package Forix\CategoryCustom\Model\Category
 */
class BitReamer extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['value' => '', 'label' => 'None'],
                ['value' => 'bits', 'label' => 'Bits'],
                ['value' => 'reamers', 'label' => 'Reamers'],
                ['value' => 'Bits/Reamers', 'label' => 'Bits/Reamers'],
            ];
        }
        return $this->_options;
    }
}
