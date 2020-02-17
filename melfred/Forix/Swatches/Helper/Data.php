<?php

namespace Forix\Swatches\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

	protected $_storeManager;

	public function __construct(
		Context $context,
		StoreManagerInterface $storeManager
	)
	{
		parent::__construct($context);
		$this->_storeManager = $storeManager;
	}

	public function getMediaUrl()
	{
		return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
	}



}