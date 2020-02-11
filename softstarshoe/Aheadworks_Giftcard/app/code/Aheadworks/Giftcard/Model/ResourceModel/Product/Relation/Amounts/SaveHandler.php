<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\ResourceModel\Product\Relation\Amounts;

use Aheadworks\Giftcard\Api\Data\AmountInterface;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\EntityManager\HydratorPool;
use Aheadworks\Giftcard\Model\Product\Type\Giftcard as ProductGiftcard;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Magento\Framework\EntityManager\EntityManager;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\Giftcard\Model\ResourceModel\Product\Relation\Amounts
 */
class SaveHandler implements ExtensionInterface
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
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param ResourceConnection $resourceConnection
     * @param MetadataPool $metadataPool
     * @param HydratorPool $hydratorPool
     * @param AttributeRepositoryInterface $attributeRepository
     * @param StoreManager $storeManager
     * @param EntityManager $entityManager
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        MetadataPool $metadataPool,
        HydratorPool $hydratorPool,
        AttributeRepositoryInterface $attributeRepository,
        StoreManager $storeManager,
        EntityManager $entityManager
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->hydratorPool = $hydratorPool;
        $this->attributeRepository = $attributeRepository;
        $this->storeManager = $storeManager;
        $this->entityManager = $entityManager;
    }

    /**
     *  {@inheritDoc}
     */
    public function execute($entity, $arguments = [])
    {
        $hydrator = $this->hydratorPool->getHydrator(ProductInterface::class);
        $entityData = $hydrator->extract($entity);
        if ($entityData['type_id'] !== ProductGiftcard::TYPE_CODE) {
            return $entity;
        }

        $amounts = !empty($entity->getExtensionAttributes()->getAwGiftcardAmounts())
            ? $entity->getExtensionAttributes()->getAwGiftcardAmounts()
            : [];
        $entityId = $entityData['entity_id'];
        $this->removeAmountsByProduct($entityId);
        $this->saveNewProductAmounts($amounts, $entityId);

        return $entity;
    }

    /**
     * Remove amounts data by product id
     *
     * @param int $entityId
     * @return int
     */
    private function removeAmountsByProduct($entityId)
    {
        $connection = $this->getConnection();
        $table = $this->resourceConnection
            ->getTableName($this->metadataPool->getMetadata(AmountInterface::class)->getEntityTable());

        return $connection->delete($table, ['entity_id = ?' => $entityId]);
    }

    /**
     * Save new product amounts data
     *
     * @param [] $amounts
     * @param int $entityId
     * @return $this
     */
    private function saveNewProductAmounts($amounts, $entityId)
    {
        foreach ($amounts as $amount) {
            /** @var AmountInterface $amount */
            $amount->setValueId('');
            $amount->setEntityId($entityId);
            $this->entityManager->save($amount);
        }
        return $this;
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
