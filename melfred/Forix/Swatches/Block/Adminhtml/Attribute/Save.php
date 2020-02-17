<?php

namespace Forix\Swatches\Block\Adminhtml\Attribute;
use Forix\Swatches\Model\Del;

class Save
{
	protected $_delete;

	public function __construct(Del $del)
	{
		$this->_delete = $del;
	}

	public function afterExecute($subject, $result) {
		if ($subject->getRequest()->getParam('swatch_input_type') == "dropdown") {
			$code  = str_replace("attr_","", $subject->getRequest()->getParam("filter_code"));
			$this->_delete->delSwatch($code);
		}
		return $result;
	}
}