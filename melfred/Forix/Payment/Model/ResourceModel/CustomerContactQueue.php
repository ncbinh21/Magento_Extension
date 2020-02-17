<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 07/12/2018
 * Time: 12:06
 */

namespace Forix\Payment\Model\ResourceModel;


class CustomerContactQueue extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('forix_payment_customercontactqueue', 'customercontactqueue_id');
    }
}