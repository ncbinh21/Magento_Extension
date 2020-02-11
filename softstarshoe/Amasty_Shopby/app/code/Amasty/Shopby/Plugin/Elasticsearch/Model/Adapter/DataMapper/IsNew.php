<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Plugin\Elasticsearch\Model\Adapter\DataMapper;

use Amasty\Shopby\Plugin\Elasticsearch\Model\Adapter\DataMapperInterface;

/**
 * Class IsNew
 * @package Amasty\Shopby\Plugin\Elasticsearch\Model\Adapter\DataMapper
 */
class IsNew implements DataMapperInterface
{
    const FIELD_NAME = 'is_new';
    const DOCUMENT_FIELD_NAME = 'news_from_date';
    const INDEX_DOCUMENT = 'document';

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var \Amasty\Shopby\Model\Layer\Filter\IsNew\Helper
     */
    private $isNewHelper;

    /**
     * @var array
     */
    private $newProductIds = [];

    /**
     * OnSale constructor.
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Amasty\Shopby\Model\Layer\Filter\IsNew\Helper $isNewHelper
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->isNewHelper = $isNewHelper;
    }

    /**
     * @param int $entityId
     * @param array $entityIndexData
     * @param int $storeId
     * @param array $context
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function map($entityId, array $entityIndexData, $storeId, $context = [])
    {
        $value = isset($context[self::INDEX_DOCUMENT][self::DOCUMENT_FIELD_NAME])
            ? $context[self::INDEX_DOCUMENT][self::DOCUMENT_FIELD_NAME] : $this->isProductNew($entityId, $storeId);
        return [self::FIELD_NAME => (int)$value];
    }

    /**
     * @param $entityId
     * @param $storeId
     * @return bool
     */
    private function isProductNew($entityId, $storeId)
    {
        return isset($this->getNewProductIds($storeId)[$entityId]);
    }

    /**
     * @return array
     */
    private function getNewProductIds($storeId)
    {
        if (!isset($this->newProductIds[$storeId]) || empty($this->newProductIds[$storeId])) {
            $this->newProductIds[$storeId] = [];
            $collection = $this->productCollectionFactory->create()->addStoreFilter($storeId);
            $this->isNewHelper->addNewFilter($collection);
            foreach ($collection as $item) {
                $this->newProductIds[$storeId][$item->getId()] = $item->getId();
            }
        }
        return $this->newProductIds[$storeId];
    }
}
