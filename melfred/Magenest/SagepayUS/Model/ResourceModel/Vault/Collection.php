<?php

namespace Magenest\SagepayUS\Model\ResourceModel\Vault;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Magenest\SagepayUS\Model\Vault', 'Magenest\SagepayUS\Model\ResourceModel\Vault');
    }
}
