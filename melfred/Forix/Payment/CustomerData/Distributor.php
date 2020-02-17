<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 13/11/2018
 * Time: 16:05
 */
namespace Forix\Payment\CustomerData;;

use Magento\Customer\CustomerData\SectionSourceInterface;
class Distributor implements SectionSourceInterface
{
    protected $_customerOrderData;
    public function __construct(\Forix\CustomerOrder\Helper\Data $customerOrderData)
    {
        $this->_customerOrderData = $customerOrderData;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getSectionData()
    {
        return [
            'is_distributor' => $this->_customerOrderData->isDistributor()
        ];
    }
}