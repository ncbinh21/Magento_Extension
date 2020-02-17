<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\Source\PageType;

use Magento\Store\Model\StoreManagerInterface;

class Index extends AbstractPage
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        StoreManagerInterface $storeManager,
        $isMultistoreMode = false,
        array $stores = []
    ) {
        parent::__construct($isMultistoreMode, $stores);
        $this->storeManager = $storeManager;
    }

    public function getAllPages($limit = 0)
    {
        $result = [];

        foreach ($this->stores as $storeId) {
            $result [] = [
                'url' => $this->storeManager->getStore($storeId)->getBaseUrl(),
                'store' => $storeId
            ];

            if (--$limit == 0) {
                break;
            }
        }

        return $result;
    }
}
