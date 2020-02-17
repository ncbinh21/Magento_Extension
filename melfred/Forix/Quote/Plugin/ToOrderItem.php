<?php

namespace Forix\Quote\Plugin;

use Magento\Quote\Model\Quote\Item\ToOrderItem as QuoteToOrderItem;
use Magento\Framework\Serialize\Serializer\Json;

class ToOrderItem {

	public function __construct(Json $serializer = null)
	{
		$this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Framework\Serialize\Serializer\Json::class);
	}

	public function aroundConvert(QuoteToOrderItem $subject,
		\Closure $proceed,
		$item,
		$data = []
	) {
		// Get Order Item
		$orderItem = $proceed($item, $data);

		$additionalOptions = $item->getOptionByCode('additional_options');


		// Check if there is any additional options in Quote Item
		if (count($additionalOptions) > 0) {
			$rigValue = "";
			$additional_options  = $this->serializer->unserialize($additionalOptions->getValue());
			foreach ($additional_options as $item) {
				if (isset($item["label"]) && $item["label"] == "Your Rig Model") {
					$rigValue =  $item["value"];
					break;
				}
			}
			if($rigValue) {
                // Get Order Item's other options
                $options = $orderItem->getProductOptions();
                if (isset($options["info_buyRequest"]["rig_model"])) {
                    $orderItem->setData("rig_model", $rigValue);
                }
            }
			// Set additional options to Order Item
			$options['additional_options'] = $additional_options;
			$orderItem->setProductOptions($options);
		}

		return $orderItem;
	}

}