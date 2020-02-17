<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 21/08/2018
 * Time: 18:10
 */

namespace Forix\Distributor\Model;


class CompanyDistributor extends \Magento\Framework\Model\AbstractModel
{
    protected $_eventPrefix = 'forix_company_distributors';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Forix\Distributor\Model\ResourceModel\CompanyDistributor::class);
    }
}