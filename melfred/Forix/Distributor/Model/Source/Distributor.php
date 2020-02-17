<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forix\Distributor\Model\Source;

/**
 * Class PaymentMethod.
 */
class Distributor implements \Magento\Framework\Data\OptionSourceInterface
{
    protected $_locationFactory;
    protected $_options;

    public function __construct(
        \Amasty\Storelocator\Model\LocationFactory $locationFactory
    )
    {
        $this->_locationFactory = $locationFactory;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        // TODO: Implement toOptionArray() method.
        if (!$this->_options) {
            $collection = $this->_locationFactory->create()->getCollection();
            $options = [];
            /**
             * @var $location \Amasty\Storelocator\Model\Location
             */
            foreach ($collection as $location) {
                $options[] = ['value' => $location->getId(), 'label' => $location->getName()];
            }
            $this->_options = $options;
        }
        return $this->_options;
    }
}
