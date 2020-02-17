<?php
/**
 * Forix_BannerAttribute extension
 *                     NOTICE OF LICENSE
 * 
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 * 
 *                     @category  Forix
 *                     @package   Forix_BannerAttribute
 *                     @copyright Copyright (c) 2016
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Forix\AdvancedAttribute\Model\ResourceModel\Option;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{


    protected function _construct()
    {
        $this->_init('Forix\AdvancedAttribute\Model\Option', 'Forix\AdvancedAttribute\Model\ResourceModel\Option');
    }

    /**
     * @return $this
     */
    public function isActiveFilter(){
        $this->addFieldToFilter('is_active', \Forix\AdvancedAttribute\Block\Adminhtml\Options\Edit\Tab\Main::IS_ACTIVE_YES);
        return $this;
    }

    /**
     * @param $option_id
     * @return $this
     */
    public function addOptionToFilter($option_id){
        $this->addFieldToFilter('option_id',['eq' => $option_id]);
        return $this;
    }

    /**
     * @param $attribute_id
     * @return $this
     */
    public function addAttributeFilter($attribute_id){
        $this->addFieldToFilter('attribute_id',['eq' => $attribute_id]);
        return $this;
    }

	public function updateData($data, $optionId) {
		$adapter = $this->getConnection();
		$adapter->update('forix_attribute_option_banner',$data, 'option_id = '.$optionId.'');
	}

}
