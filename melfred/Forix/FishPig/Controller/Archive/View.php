<?php
/**
 *
**/
namespace Forix\FishPig\Controller\Archive;

use FishPig\WordPress\Model\ArchiveFactory;

class View extends \FishPig\WordPress\Controller\Archive\View
{

	protected $archiveFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Registry $registry,
		\FishPig\WordPress\Model\App $app,
		\FishPig\WordPress\Model\App\Factory $factory,
		ArchiveFactory $archiveFactory
	) {
		parent::__construct($context,$registry,$app,$factory);
		$this->archiveFactory = $archiveFactory;
	}

	protected function _getEntity()
	{
		return $this->archiveFactory->create()->load(
			trim($this->_request->getParam('year') . '/' . $this->_request->getParam('month') . '/' . $this->_request->getParam('day'), '/')
		);
	}
	

}
