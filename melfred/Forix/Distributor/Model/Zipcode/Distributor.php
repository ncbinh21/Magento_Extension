<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 20/08/2018
 * Time: 15:08
 */

namespace Forix\Distributor\Model\Zipcode;


class Distributor  extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    protected $_locationFactory;

    public function __construct(
        \Amasty\Storelocator\Model\LocationFactory $locationFactory
    )
    {
        $this->_locationFactory = $locationFactory;
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            /**
             * @var $locationCollection \Amasty\Storelocator\Model\ResourceModel\Location\Collection
             * @var $location \Amasty\Storelocator\Model\Location
             */
            $locationCollection = $this->_locationFactory->create()->getCollection();
            foreach ($locationCollection as $location){
                $this->_options[] = ['value' => $location->getId(), 'label' => $location->getName()];
            }
        }
        return $this->_options;
    }
}