<?php

namespace Forix\Swatches\Block\Adminhtml\Attribute\Edit\Options;

class Visual extends \Magento\Swatches\Block\Adminhtml\Attribute\Edit\Options\Visual
{

	public function getJsonConfig()
	{

		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$helper = $objectManager->create('Forix\Swatches\Helper\Data');

		$values = [];
		foreach ($this->getOptionValues() as $value) {
			$swatch = $value->getData('defaultswatch0');
			$pos = strpos($swatch, '#');
			if ($pos === false) {
				$swImage = $helper->getMediaUrl().'attribute/swatch'.$value->getData('defaultswatch0');
				$value->setData('swatch0','background-image: url('.$swImage.')');
			} else {

				$value->setData('swatch0','background:'.$value->getData('defaultswatch0'));
			}
			$values[] = $value->getData();
		}

		$data = [
			'attributesData' => $values,
			'uploadActionUrl' => $this->getUrl('swatches/iframe/show'),
			'isSortable' => (int)(!$this->getReadOnly() && !$this->canManageOptionDefaultOnly()),
			'isReadOnly' => (int)$this->getReadOnly()
		];

		return json_encode($data);
	}
}