<?php

namespace Magenest\SagepayUS\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Vault extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('magenest_sagepayus_vault', 'id');
    }
}
