<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\Source\PageType;

use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;

abstract class Rewrite extends Emulated
{
    /**
     * @var UrlRewriteCollectionFactory
     */
    private $rewriteCollectionFactory;

    protected $rewriteType;

    public function __construct(
        UrlRewriteCollectionFactory $rewriteCollectionFactory,
        \Magento\Framework\Url $url,
        \Magento\Store\Model\App\Emulation $appEmulation,
        $isMultistoreMode = false,
        array $stores = [],
        \Closure $filterCollection = null
    ) {
        parent::__construct($url, $appEmulation, $isMultistoreMode, $stores, $filterCollection);

        $this->rewriteCollectionFactory = $rewriteCollectionFactory;
    }

    protected function getEntityCollection($storeId)
    {
        /** @var \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection $rewriteCollection */
        $rewriteCollection = $this->rewriteCollectionFactory->create();

        $rewriteCollection
            ->addFieldToFilter('redirect_type', 0)
            ->addFieldToFilter('entity_type', $this->rewriteType);
        if ($storeId) {
            $rewriteCollection->addStoreFilter($storeId);
        }

        return $rewriteCollection;
    }

    /**
     * @param \Magento\Cms\Model\Page $entity
     * @param                         $storeId
     *
     * @return bool|string
     */
    protected function getUrl($entity, $storeId)
    {
        return $entity->getData('request_path');
    }
}
