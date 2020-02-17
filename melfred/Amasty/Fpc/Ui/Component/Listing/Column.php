<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Ui\Component\Listing;

use Magento\Framework\Option\ArrayInterface;

class Column extends \Magento\Ui\Component\Listing\Columns\Column
{
    public function prepare()
    {
        $options = $this->getData('options');

        if ($options instanceof ArrayInterface) {

            $options = $options->toOptionArray();
            array_unshift($options, ['value' => null, 'label' => __('[Default]')]);

            $this->setData('options', $options);
        }

        parent::prepare();
    }
}
