<?php

namespace Forix\RequisitionList\Plugin;


use Forix\Shopby\Model\ResourceModel\ResourceHelperFactory;

class Options
{

	protected $_collectionFactory;

	public function __construct(ResourceHelperFactory $collectionFactory) {
		$this->_collectionFactory = $collectionFactory;
	}

	public function afterGetConfiguredOptions($subject, $result)  {
		$items   = $subject->getItem();
		$options = $items->getOptions();
		$output  = [];
		if (isset($options["info_buyRequest"]["rig"])) {
			$rigData  = $options["info_buyRequest"]["rig"];
			$attrId   = array_keys($rigData)[0];
			$valueRig = $rigData[$attrId];
			$optopRigArr = $this->_collectionFactory->create()->getOptionById($valueRig);
			if (!empty($optopRigArr)) {
				$output[] = [
					'label'     => 'Your Rig',
					'value'     => $optopRigArr['value'],
					'option_id' => $attrId,
					'option_value'=>$optopRigArr['option_id']
				];
				return array_merge($output, $result);
			}

		}

		return $result;
	}
}