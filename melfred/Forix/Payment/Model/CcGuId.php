<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: melfredborzall.local
 */

namespace Forix\Payment\Model;


class CcGuId extends \Magento\Framework\Model\AbstractModel
{
    protected $_eventPrefix = 'ccguid';

    protected function _construct()
    {
        $this->_init('Forix\Payment\Model\ResourceModel\CcGuId');
    }
}