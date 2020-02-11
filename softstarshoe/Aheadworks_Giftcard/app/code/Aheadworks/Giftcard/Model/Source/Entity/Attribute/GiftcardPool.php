<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Source\Entity\Attribute;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\Data\Collection;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\DB\Ddl\Table;
use Magento\Eav\Model\ResourceModel\Entity\AttributeFactory;
use Aheadworks\Giftcard\Model\ResourceModel\Pool\Collection as PoolCollection;

/**
 * Class GiftcardPool
 *
 * @package Aheadworks\Giftcard\Model\Source\Entity\Attribute
 */
class GiftcardPool extends AbstractSource
{
    /**
     * @var PoolCollection
     */
    private $poolCollection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var AttributeFactory
     */
    private $eavAttributeFactory;

    /**
     * @param PoolCollection $poolCollection
     * @param MetadataPool $metadataPool
     * @param AttributeFactory $eavAttributeFactory
     */
    public function __construct(
        PoolCollection $poolCollection,
        MetadataPool $metadataPool,
        AttributeFactory $eavAttributeFactory
    ) {
        $this->poolCollection = $poolCollection;
        $this->metadataPool = $metadataPool;
        $this->eavAttributeFactory = $eavAttributeFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = $this->poolCollection->toOptionArray();
        }
        return $this->_options;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }

    /**
     * {@inheritdoc}
     */
    public function getFlatColumns()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();

        return [
            $attributeCode => [
                'unsigned' => true,
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Aheadworks Gift Card Pool',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFlatUpdateSelect($store)
    {
        return $this->eavAttributeFactory->create()->getFlatUpdateSelect($this->getAttribute(), $store);
    }

    /**
     * Add Value Sort To Collection Select
     *
     * @param AbstractCollection $collection
     * @param string $dir direction
     * @return $this
     */
    public function addValueSortToCollection($collection, $dir = Collection::SORT_ORDER_DESC)
    {
        $linkField = $this->metadataPool->getMetadata(CategoryInterface::class)->getLinkField();

        $attributeCode = $this->getAttribute()->getAttributeCode();
        $attributeId = $this->getAttribute()->getId();
        $attributeTable = $this->getAttribute()->getBackend()->getTable();

        $tableName = $attributeCode . '_t';
        $collection->getSelect()
            ->joinLeft(
                [$tableName => $attributeTable],
                "e.{$linkField}={$tableName}.{$linkField}" .
                " AND {$tableName}.attribute_id='{$attributeId}'" .
                " AND {$tableName}.store_id='0'",
                []
            );
        $collection->getSelect()->order($tableName . '.value ' . $dir);
        return $this;
    }
}
