<?php

namespace Forix\Custom\Model\Override;

use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute;

class ConfigurableAttributeData extends \Magento\ConfigurableProduct\Model\ConfigurableAttributeData
{

	protected $_optionPosition = [];
	protected function getAttributeOptionPositions($attribute)
	{
		if(empty($this->_optionPosition)) {
			$connection = $attribute->getResource()->getConnection();
			$select = $connection->select()
				->from(['attr_opt' => $connection->getTableName('eav_attribute_option')], ['option_id', 'sort_order']);
			/*->where('attribute_id = ?', $attribute->getAttributeId());*/
			$this->_optionPosition = $connection->fetchPairs($select);
		}
		return $this->_optionPosition;
	}

	protected function getAttributeOptionsData($attribute, $config)
	{

		$attributeOptionsData = [];
		$positions = $this->getAttributeOptionPositions($attribute);
		asort($positions);
		foreach ($attribute->getOptions() as $attributeOption) {
			$optionId = $attributeOption['value_index'];
			$position = 0;
			if(isset($positions[$optionId])) {
				$position = $positions[$optionId];
			}
			while (isset($attributeOptionsData[$position])) {
				$position++;
			}
			$attributeOptionsData[$position] = [
				'id' => $optionId,
				'label' => $attributeOption['label'],
				'products' => isset($config[$attribute->getAttributeId()][$optionId])
					? $config[$attribute->getAttributeId()][$optionId]
					: [],
			];
		}
		ksort($attributeOptionsData);
		/*usort($attributeOptionsData, function($item1, $item2){
			return $item1['label'] <=> $item2['label'];
		});*/

		return array_values($attributeOptionsData);
	}
}