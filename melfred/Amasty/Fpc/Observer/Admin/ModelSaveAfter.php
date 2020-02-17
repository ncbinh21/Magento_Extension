<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Observer\Admin;

use Amasty\Fpc\Model\Config;
use Amasty\Fpc\Model\Refresher;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ModelSaveAfter implements ObserverInterface
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var Refresher
     */
    private $refresher;

    public function __construct(
        Config $config,
        Refresher $refresher
    ) {
        $this->config = $config;
        $this->refresher = $refresher;
    }

    public function execute(Observer $observer)
    {
        if (!$this->config->isSetFlag('general/auto_update')) {
            return;
        }

        $object = $observer->getData('object');

        if ($object instanceof PageInterface) {
            if ($this->refresher->isIndexPage($object->getIdentifier())) {
                $this->refresher->queueIndexPage();
            } else {
                $this->refresher->queueCmsPage($object->getIdentifier());
            }
        } else if ($object instanceof ProductInterface) {
            $this->refresher->queueProductPage($object->getId());
        } else if ($object instanceof CategoryInterface) {
            $this->refresher->queueCategoryPage($object->getId());
        }
    }
}
