<?php

namespace Forix\Distributor\Controller\Adminhtml\Customer;

use Magento\Framework\Controller\ResultFactory;

class NewAction extends \Magento\Backend\App\Action
{
	public function execute() {
		return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
	}
}