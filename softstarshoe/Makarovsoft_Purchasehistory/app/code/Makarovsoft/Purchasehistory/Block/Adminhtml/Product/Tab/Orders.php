<?php
namespace Makarovsoft\Purchasehistory\Block\Adminhtml\Product\Tab;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Orders extends \Makarovsoft\Purchasehistory\Block\Adminhtml\Product\Grid
{
    /**
     * Hide grid mass action elements
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        //return parent::_prepareMassaction();
        return $this;
    }

    /**
     * Determine ajax url for grid refresh
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('makarovsoft_purchasehistory/index/orders', ['_current' => true]);
    }
}
