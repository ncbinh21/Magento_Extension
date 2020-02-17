<?php
/**
 * 
**/
namespace Forix\FishPig\Model\App;
use FishPig\WordPress\Model\Config;
use Magento\Store\Model\StoreManagerInterface;
use Forix\FishPig\Helper\Data;

/**
 * Generate all WordPress URLs 
**/
class Url extends \FishPig\WordPress\Model\App\Url
{

	protected $helper;

	public function __construct(
		Config $config,
		StoreManagerInterface $storeManager,
		Data $helper
	)
	{
		parent::__construct($config,$storeManager);
		$this->helper = $helper;
	}

	public function getHomeUrl()
	{
		$home = $this->helper->getConfigValue('wordpress/setup/home_url');
		return rtrim($home, '/');
	}
}
