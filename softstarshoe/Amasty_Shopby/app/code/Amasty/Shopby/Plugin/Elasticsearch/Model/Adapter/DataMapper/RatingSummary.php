<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Plugin\Elasticsearch\Model\Adapter\DataMapper;

use Amasty\Shopby\Plugin\Elasticsearch\Model\Adapter\DataMapperInterface;

/**
 * Class RatingSummary
 * @package Amasty\Shopby\Plugin\Elasticsearch\Model\Adapter\DataMapper
 */
class RatingSummary implements DataMapperInterface
{
    const FIELD_NAME = 'rating_summary';

    /**
     * @var \Magento\Review\Model\ReviewFactory
     */
    private $reviewFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    private $productFactory;

    /**
     * RatingSummaryDataMapper constructor.
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->reviewFactory = $reviewFactory;
        $this->productFactory = $productFactory;
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
        /**
         * @var \Magento\Catalog\Model\Product $product
         */
        $product = $this->productFactory->create(['data' => ['entity_id' => $entityId]]);

        $this->reviewFactory->create()->getEntitySummary($product, $storeId);
        return [self::FIELD_NAME => $product->getRatingSummary()->getRatingSummary()];
    }
}
