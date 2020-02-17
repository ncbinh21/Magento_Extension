<?php
/**
 *
 **/

namespace Forix\FishPig\Controller\User;

use FishPig\WordPress\Model\UserFactory;

class View extends \FishPig\WordPress\Controller\User\View
{

	protected $userFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Registry $registry,
		\FishPig\WordPress\Model\App $app,
		\FishPig\WordPress\Model\App\Factory $factory,
		UserFactory $UserFactory
	) {
		parent::__construct($context,$registry,$app,$factory);
		$this->userFactory = $UserFactory;
	}

	protected function _getEntity()
	{
		$object = $this->userFactory->create()->load(
			$this->getRequest()->getParam('author'),
			'user_nicename'
		);

		return $object->getId() ? $object : false;
	}


}
