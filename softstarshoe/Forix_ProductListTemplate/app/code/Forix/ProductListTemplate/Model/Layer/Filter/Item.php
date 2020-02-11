<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 3/13/2018
 * Time: 5:07 PM
 */

namespace Forix\ProductListTemplate\Model\Layer\Filter;

class Item extends \Amasty\Shopby\Model\Layer\Filter\Item
{
	/**
	 * Get filter item url for clearance
	 *
	 * @return string
	 */
	public function getUrl2()
	{
		return $this->urlBuilderHelper->buildUrl($this->getFilter(), $this->getValue());
	}
}