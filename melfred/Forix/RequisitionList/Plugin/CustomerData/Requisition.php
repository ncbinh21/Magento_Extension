<?php

namespace Forix\RequisitionList\Plugin\CustomerData;

use Magento\Authorization\Model\UserContextInterface;
use Magento\RequisitionList\Model\ResourceModel\RequisitionList\CollectionFactory;

class Requisition
{

	protected $userContext;
	protected $collectionFactory;

	public function __construct(
		UserContextInterface $userContext,
		CollectionFactory $collectionFactory
	)
	{
		$this->userContext = $userContext;
		$this->collectionFactory = $collectionFactory;
	}

	public function afterGetSectionData($subject, $result)
	{
		if ($result["is_enabled"]) {
			if (isset($result["items"]) && empty($result["items"])) {
				$customerId = $this->userContext->getUserId();
				$lists = $this->collectionFactory->create()->addFieldToFilter("customer_id", $customerId);
				$items = [];
				foreach ($lists as $list) {
					$items[] = [
						'id' => $list->getId(),
						'name' => $list->getName()
					];
				}
				$result["items"] = $items;
			}
		}
		return $result;

	}
}