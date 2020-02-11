<?php

namespace Forix\Catalog\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class ChangeLayoutProduct implements ObserverInterface
{
    public function __construct(
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $layout = $observer->getEvent()->getLayout();
        if($this->coreRegistry->registry('current_product')){
            if($this->coreRegistry->registry('current_product')->getTypeId() == 'aw_giftcard') {
                $layout->getUpdate()->addHandle('catalog_product_remove_block');
            }
        }
    }
}