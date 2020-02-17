<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\Source\PageType;

use Amasty\Fpc\Model\Config;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

class Factory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        ObjectManagerInterface $objectManager,
        Config $config,
        StoreManagerInterface $storeManager
    ) {
        $this->objectManager = $objectManager;
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * @param string $type
     * @param array  $params
     *
     * @return AbstractPage
     */
    public function create($type, $params = [])
    {
        $stores = $this->config->getStores();

        if (count($stores) <= 1) {
            $isMultistoreMode = false;
        } else {
            $fistStoreUrl = $this->storeManager->getStore($stores[0])->getBaseUrl();
            $secondStoreUrl = $this->storeManager->getStore($stores[1])->getBaseUrl();

            $isMultistoreMode = $fistStoreUrl != $secondStoreUrl;
        }

        if (!$isMultistoreMode || !$stores) {
            $stores = [null];
        }

        $params = array_merge([
            'isMultistoreMode' => $isMultistoreMode,
            'stores' => $stores
        ], $params);

        return $this->objectManager->create('\Amasty\Fpc\Model\Source\PageType\\' . ucfirst($type), $params);
    }
}
