<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Makarovsoft\Purchasehistory\Block\Adminhtml\Product;

class Orders extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Button\Generic
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Export Orders'),
            'on_click' => sprintf("location.href = '%s';", $this->getUrl('makarovsoft_purchasehistory/export/orders/', ['id' => $this->getProduct()->getId()])),
            'sort_order' => 11
        ];
    }
}
