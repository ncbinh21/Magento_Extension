<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 21/08/2018
 * Time: 18:10
 */

namespace Forix\Distributor\Model\ResourceModel;


class CompanyDistributor extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('company_distributors', 'row_id');
    }
}