<?php

namespace Forix\RequisitionList\Plugin;

use Forix\Configurable\Helper\Data;

class RequisitionListProduct
{
	protected $_ConfigurableHelper;

	public function __construct(
		Data $data
	)
	{
		$this->_ConfigurableHelper = $data;
	}

	public function afterPrepareProductData($subject, $result) {
		$rigAttId  = $this->_ConfigurableHelper->getIdAttribute('mb_rig_model');
		if (isset($result["options"]["super_attribute"][$rigAttId])) {
			$value = $result["options"]["super_attribute"][$rigAttId];
			$super_attribute  = $result["options"]["super_attribute"];
			unset($super_attribute[$rigAttId]);
			$rigData = [$rigAttId => $value];
			$options = $result["options"];
			$options["super_attribute"] = $super_attribute;
			$options["rig"] = $rigData;
			$result->setData('options', $options);

		}

		return $result;
	}
}