<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

namespace Forix\FishPig\Block\Sidebar\Widget;

class Categories extends \FishPig\WordPress\Block\Sidebar\Widget\Categories
{
	/**
	 * Set the posts collection
	 *
	 */
	protected function _beforeToHtml()
	{
		if (!$this->getTemplate()) {
			$this->setTemplate('FishPig_WordPress::sidebar/widget/categories.phtml');
		}

		return parent::_beforeToHtml();
	}

	public function getCoutAllItem() {
		$collection = $this->_factory->getFactory('Post')->create()->getCollection()
			->addFieldToSelect('id')
			->addFieldToFilter("post_status",["in"=>["publish","protected"]])
			->addFieldToFilter("post_type",["eq"=>"post"]);
		return $collection->count();
	}


}
