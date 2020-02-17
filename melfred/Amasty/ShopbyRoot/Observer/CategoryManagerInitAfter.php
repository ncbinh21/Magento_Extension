<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyRoot
 */


namespace Amasty\ShopbyRoot\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class CategoryManagerInitAfter
 * @package Amasty\ShopbyRoot\Observer
 */
class CategoryManagerInitAfter implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * CategoryManagerInitAfter constructor.
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\Registry $registry
    ) {
        $this->coreRegistry = $registry;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->coreRegistry->registry('amasty_shopby_root_category_index')) {
            $this->coreRegistry->register('amasty_shopby_root_category_index', true);
        }
    }
}
