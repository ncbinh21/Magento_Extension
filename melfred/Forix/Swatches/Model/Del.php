<?php

namespace Forix\Swatches\Model;

use Magento\Framework\App\ResourceConnection;

class Del {

	protected $_connection;

	public function __construct(ResourceConnection $connection)
	{
		$this->_connection = $connection;
	}

	public function delSwatch($code) {
		$query = "DELETE s.* FROM eav_attribute_option_swatch s
						INNER JOIN eav_attribute_option o on o.option_id = s.option_id
						INNER JOIN eav_attribute a on a.attribute_id = o.attribute_id
						WHERE a.attribute_code IN ('$code');";
		$this->_connection->getConnection()->query($query);
	}
}

