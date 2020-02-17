<?php

namespace Forix\Distributor\Model\ResourceModel\Customer\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Collection extends \Magento\Customer\Model\ResourceModel\Grid\Collection
{
	/**
	 * @var \Magento\Framework\App\Config\ScopeConfigInterface
	 */
	protected $_scopeConfig;


	public function __construct(
		ScopeConfigInterface $scopeConfig,
		EntityFactory $entityFactory,
	    Logger $logger,
		FetchStrategy $fetchStrategy,
		EventManager $eventManager,
		string $mainTable = 'customer_grid_flat',
		string $resourceModel = \Magento\Customer\Model\ResourceModel\Customer::class

	)
	{
		$this->_scopeConfig = $scopeConfig;
		parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, 'customer_grid_flat', \Magento\Customer\Model\ResourceModel\Customer::class);
	}

	protected function _initSelect()
	{
		$customerGroup = $this->_scopeConfig->getValue("forix_customer/customergroup/distributor_group");
		$customerGroup = explode(",", $customerGroup);
		parent::_initSelect();
		$this->addFieldToFilter("group_id",['in' => $customerGroup]);
		$this->getSelect()->joinLeft(
			["cg"=>"customer_group"],"cg.customer_group_id = main_table.group_id","cg.customer_group_code"
		);
		return $this;
	}
}