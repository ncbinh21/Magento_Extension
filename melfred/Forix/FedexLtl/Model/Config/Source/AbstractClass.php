<?php
/**
 * Created by PhpStorm.
 * User: nghia
 * Date: 20/03/2019
 * Time: 19:00
 */

namespace Forix\FedexLtl\Model\Config\Source;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Option\ArrayInterface;

class AbstractClass implements OptionSourceInterface, ArrayInterface
{

    protected $_classes = [];
    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array_combine($this->_classes, $this->_classes);
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach($this->_classes as $_class){
            $options[] = [
                'label' => $_class,
                'value' => $_class,
            ];
        }
        return $options;
    }
}
