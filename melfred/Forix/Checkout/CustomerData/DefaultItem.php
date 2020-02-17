<?php

namespace Forix\Checkout\CustomerData;

use Forix\Checkout\Helper\Data;

class DefaultItem
{

	protected $_helper;

	public function __construct(Data $helper)
	{
		$this->_helper = $helper;
	}

	public function afterGetItemData($subject, $result)
	{
		if (isset($result["options"]) && !empty($result["options"]))
		{
			$options = $result["options"];
			$op = $this->_helper->arrangeAttributeOption($options);
			$result["options"] = $op;
		}
		return $result;
	}

}