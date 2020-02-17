<?php

namespace Forix\Quote\Plugin;

use Forix\Base\Helper\Data;


class Item
{
	protected $_helper;

	public function __construct(Data $helper)
	{
		$this->_helper = $helper;
	}

	public function afterGetBuyRequest($subject,$result)
	{
		if (isset($result["rig_model"]) && !empty($result["rig_model"]))
		{
			$rigArr 	= $result["rig_model"];
			$attr   	= $result["super_attribute"];
			$idRigModel = array_keys($rigArr)[0];
			$attr[$idRigModel] = $rigArr[$idRigModel];
			$outPut[$idRigModel] = $rigArr[$idRigModel];
			foreach ($attr as $k=>$value)
			{
				$outPut[$k] = $value;
			}
			$paramsRig = [
				'rig_selected' => [$idRigModel => $rigArr[$idRigModel]]
			];
			$this->_helper->setParams($paramsRig);
			$result->setData('super_attribute', $outPut);
		}

		return $result;
	}



}