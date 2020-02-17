<?php

namespace Forix\Checkout\Plugin\Cart;

use Forix\Configurable\Helper\Data;

class UpdateItemOptions
{
	protected $_helper;

	public function __construct(Data $helper)
	{
		$this->_helper = $helper;
	}

	public function beforeExecute($subject)
	{
		$params    = $subject->getRequest()->getParams();
		$attrRigId = $this->_helper->getIdAttribute('mb_rig_model');
		if (isset($params["super_attribute"][$attrRigId]))
		{
			$valueRig  = $params["super_attribute"][$attrRigId];
			$params["rig_model"] = [
				$attrRigId => $valueRig
			];
			if (isset($params["super_attribute"][$attrRigId])) {
				unset($params["super_attribute"][$attrRigId]);
			}
			$subject->getRequest()->setParams($params);
		}

	}
}