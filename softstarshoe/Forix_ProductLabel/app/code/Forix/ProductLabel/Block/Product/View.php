<?php
/*************************************************
 * *
 *  *
 *  * Copyright © 2017 Forix. All rights reserved.
 *  * See COPYING.txt for license details.
 *
 */

namespace Forix\ProductLabel\Block\Product;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Forix\ProductLabel\Helper\Data as HelperData;

/**
 * Label View block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */

class View extends AbstractProduct
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * View constructor.
     * @param Context $context
     * @param HelperData $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperData = $helperData;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        $isEnabled = (bool)$this->_scopeConfig->isSetFlag('productlabel/settings/active');
        if ($isEnabled && $this->getProduct()->getId()) {
            return parent::toHtml();
        } else {
            return '';
        }
    }

    /**
     * @return \Magento\Framework\Data\Collection
     */
    public function getProductLabel()
    {
        $helper = $this->helperData->getLabels($this->getProduct());
        return $helper;
    }
}
