<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 07/12/2018
 * Time: 12:06
 */

namespace Forix\Payment\Model;


class CustomerContactQueue extends \Magento\Framework\Model\AbstractModel
{
    const STATUS_PUSHED = 1;
    const STATUS_NOT_PUSHED = 0;
    protected $_eventPrefix = 'forix_payment_customercontactqueue';
    protected $_idFieldName = 'customercontactqueue_id';
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\CustomerContactQueue::class);
    }
}