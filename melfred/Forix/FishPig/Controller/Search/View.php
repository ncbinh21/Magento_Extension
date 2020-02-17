<?php
/**
 *
 **/

namespace Forix\FishPig\Controller\Search;

use FishPig\WordPress\Model\SearchFactory;

class View extends \FishPig\WordPress\Controller\Search\View
{

	protected $searchFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Registry $registry,
		\FishPig\WordPress\Model\App $app,
		\FishPig\WordPress\Model\App\Factory $factory,
		SearchFactory $searchFactory
	) {
		parent::__construct($context,$registry,$app,$factory);
		$this->searchFactory = $searchFactory;
	}

	public function _getEntity()
	{
		return $this->searchFactory->create();
	}


}
