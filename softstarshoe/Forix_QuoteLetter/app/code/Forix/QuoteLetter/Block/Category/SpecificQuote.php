<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2 - Soft StartShoes
 * Date: 1/30/18
 * Time: 2:43 PM
 */

namespace Forix\QuoteLetter\Block\Category;

class SpecificQuote extends \Forix\QuoteLetter\Block\AbstractSpecificQuote
{

    /**
     * @return \Magento\Catalog\Model\AbstractModel
     */
    public function getSource()
    {
        if (!$this->hasData('source')) {
            $this->setData('source', $this->_coreRegistry->registry('current_category'));
        }
        return $this->getData('source');
    }

    /**
     * @param $collection \Forix\QuoteLetter\Model\ResourceModel\QuoteLetter\Collection
     * @return \Forix\QuoteLetter\Model\ResourceModel\QuoteLetter\Collection
     */
    public function addSourceToFilter($collection)
    {
        if($this->getSource()) {
            // TODO: Implement addSourceToFilter() method.
            $collection->addCategoryToFilter([$this->getSource()->getId()]);
        }
        return $collection;
    }

}