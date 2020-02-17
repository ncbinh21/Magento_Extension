<?php

namespace Forix\Distributor\Model\ResourceModel\Customer;

class Customer extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	protected function _construct()
	{
		$this->_init('customer_entity', 'entity_id');
	}
}