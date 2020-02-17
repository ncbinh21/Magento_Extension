<?php

namespace Forix\Shopby\Block;

use Magento\Framework\View\Element\Template;
use Forix\Shopby\Helper\Data;

class Title extends  \Magento\Framework\View\Element\Template
{

	protected $_resourceHelperFactory;
	protected $_helper;


	public function __construct(
		Template\Context $context,
        Data $helper,
        \Forix\Shopby\Model\ResourceModel\ResourceHelperFactory $resourceHelperFactory,
		array $data = []
	)
	{
		parent::__construct($context, $data);
		$this->_helper = $helper;
		$this->_resourceHelperFactory = $resourceHelperFactory;
	}

	public function getTitle()
	{
		$title  = "Melfred Borzall";
		$params = $this->_helper->getRequestParams();

		$option = $this->_resourceHelperFactory->create()->getOptionIdByLabel($params["rig_title"]);
		if (isset($option['option_id'])) {
			$optionValue = $this->_helper->getAmastyOptionSetting($option['option_id']);
			$title = $optionValue->getData('rig_description');
		}

		return $title;
	}
}
