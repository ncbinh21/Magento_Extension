<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 07/12/2018
 * Time: 12:07
 */

namespace Forix\Payment\Model\ResourceModel\CustomerContactQueue;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Forix\Payment\Model\CustomerContactQueue::class,
            \Forix\Payment\Model\ResourceModel\CustomerContactQueue::class
        );
    }

    public function getAddEmailToFilter($email){
        $this->addFieldToFilter('customer_email', $email);
        return $this;
    }

    public function getContactCodeFilter($customerContactCode){
        $this->addFieldToFilter('contact_code', $customerContactCode);
        return $this;
    }
}