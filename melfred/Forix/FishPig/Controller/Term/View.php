<?php
/**
 *
 **/

namespace Forix\FishPig\Controller\Term;

use FishPig\WordPress\Model\TermFactory;

class View extends \FishPig\WordPress\Controller\Term\View
{

	protected $termFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Registry $registry,
		\FishPig\WordPress\Model\App $app,
		\FishPig\WordPress\Model\App\Factory $factory,
		TermFactory $termFactoryFactory
	) {
		parent::__construct($context,$registry,$app,$factory);
		$this->termFactory= $termFactoryFactory;
	}


	protected function _getEntity()
	{
		
		$object = $this->termFactory->create()->load($this->getRequest()->getParam('id'));

		return $object->getId() ? $object : false;
	}


}
