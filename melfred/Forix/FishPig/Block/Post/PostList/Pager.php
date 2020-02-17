<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

namespace Forix\FishPig\Block\Post\PostList;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template\Context;
use FishPig\WordPress\Model\App;
use FishPig\WordPress\Model\Config;


class Pager extends \FishPig\WordPress\Block\Post\PostList\Pager
{
	protected $_scopeConfig;

	public function __construct(Context $context, App $app, Config $config, ScopeConfigInterface $scopeConfig)
	{
		parent::__construct($context, $app, $config);
		$this->_scopeConfig = $scopeConfig;

	}

	protected function _construct()
	{

		$this->setPageVarName('page');
		$baseLimit = 12;
		if ($this->_scopeConfig->getValue('wordpress/setup/per_page')!="") {
			$baseLimit = $this->_scopeConfig->getValue('wordpress/setup/per_page');
		}
		$this->setDefaultLimit($baseLimit);
		$this->setLimit($baseLimit);

		$this->setAvailableLimit(array(
			$baseLimit => $baseLimit,
		));

		$this->setFrameLength(5);
	}

	protected $_template = 'Forix_FishPig::html/pager.phtml';

}
