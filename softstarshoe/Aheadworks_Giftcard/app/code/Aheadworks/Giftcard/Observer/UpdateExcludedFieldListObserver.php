<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Observer;

use Aheadworks\Giftcard\Api\Data\ProductAttributeInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

/**
 * Class UpdateExcludedFieldListObserver
 *
 * @package Aheadworks\Giftcard\Observer
 */
class UpdateExcludedFieldListObserver implements ObserverInterface
{
    /**
     * Exclude Gift Card attributes from Update Attributes mass-action form
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $block = $observer->getEvent()->getObject();
        $list = $block->getFormExcludedFieldList();
        $excludedAttributes = [
            ProductAttributeInterface::CODE_AW_GC_EMAIL_TEMPLATES,
            ProductAttributeInterface::CODE_AW_GC_AMOUNTS,
        ];
        $list = array_merge($list, $excludedAttributes);
        $block->setFormExcludedFieldList($list);
        return $this;
    }
}
