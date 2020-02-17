<?php

namespace Forix\FishPig\Controller\Homepage;

use FishPig\WordPress\Model\HomepageFactory;

class View extends \FishPig\WordPress\Controller\Homepage\View
{

	protected $homepageFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Registry $registry,
		\FishPig\WordPress\Model\App $app,
		\FishPig\WordPress\Model\App\Factory $factory,
		HomepageFactory $homepageFactory
	) {
		parent::__construct($context,$registry,$app,$factory);
		$this->homepageFactory = $homepageFactory;
	}

	protected function _getEntity()
	{

		return $this->homepageFactory->create();
	}

}