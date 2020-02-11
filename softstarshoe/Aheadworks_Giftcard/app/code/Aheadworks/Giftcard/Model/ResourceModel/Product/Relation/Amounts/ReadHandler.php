<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\ResourceModel\Product\Relation\Amounts;

use Aheadworks\Giftcard\Api\Data\AmountInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\EntityManager\HydratorPool;
use Aheadworks\Giftcard\Model\Product\Type\Giftcard as ProductGiftcard;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Aheadworks\Giftcard\Api\Data\ProductAttributeInterface;

/**
 * Class ReadHandler
 *
 * @package Aheadworks\Giftcard\Model\ResourceModel\Product\Relation\Amounts
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var HydratorPool
     */
    private $hydratorPool;

    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var StoreManager
     */
    private $storeManager;

    /**
     * @param ResourceConnection $resourceConnection
     * @param MetadataPool $metadataPool
     * @param HydratorPool $hydratorPool
     * @param AttributeRepositoryInterface $attributeRepository
     * @param StoreManager $storeManager
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        MetadataPool $metadataPool,
        HydratorPool $hydratorPool,
        AttributeRepositoryInterface $attributeRepository,
        StoreManager $storeManager
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->hydratorPool = $hydratorPool;
        $this->attributeRepository = $attributeRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        $metadata = $this->metadataPool->getMetadata(ProductInterface::class);
        $hydrator = $this->hydratorPool->getHydrator(ProductInterface::class);
        $entityData = $hydrator->extract($entity);
        if ($entityData['type_id'] !== ProductGiftcard::TYPE_CODE) {
            return $entity;
        }

        $attribute = $this->attributeRepository->get(
            $metadata->getEavEntityType(),
            ProductAttributeInterface::CODE_AW_GC_AMOUNTS
        );
        $websiteId = null;
        if (isset($entityData['store_id'])) {
            $websiteId = $this->storeManager->getStore($entityData['store_id'])->getWebsiteId();
        }
        $amounts = $this->getAmountsByProduct($entityData['entity_id'], $websiteId);
        $entityData[$attribute->getAttributeCode()] = $amounts;
        $entity = $hydrator->hydrate($entity, $entityData);
        return $entity;
    }

    /**
     * Retrieve product amounts
     *
     * @param int $entityId
     * @param int|null $websiteId
     * @return array
     */
    private function getAmountsByProduct($entityId, $websiteId = null)
    {
        $connection = $this->getConnection();
        $table = $this->resourceConnection
            ->getTableName($this->metadataPool->getMetadata(AmountInterface::class)->getEntityTable());
        $bind = ['entity_id' => $entityId];
        $columns = [
            'value_id',
            'price' => 'value',
            'website_id',
        ];

        $select = $connection->select()->from($table, $columns)->where('entity_id = :entity_id');
        if ($websiteId) {
            $select->where('website_id IN (0, :website_id)');
            $bind['website_id'] = $websiteId;
        }
        return $connection->fetchAll($select, $bind);
    }

    /**
     * Get connection
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     * @throws \Exception
     */
    private function getConnection()
    {
        return $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(AmountInterface::class)->getEntityConnectionName()
        );
    }
}
