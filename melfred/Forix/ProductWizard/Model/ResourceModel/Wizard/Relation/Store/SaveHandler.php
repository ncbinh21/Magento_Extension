<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Forix\ProductWizard\Model\ResourceModel\Wizard\Relation\Store;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Cms\Api\Data\PageInterface;
use Forix\ProductWizard\Model\ResourceModel\Wizard;
use Magento\Framework\EntityManager\MetadataPool;
use \Forix\ProductWizard\Api\Data\WizardInterface;
/**
 * Class SaveHandler
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var Wizard
     */
    protected $resourcePage;

    /**
     * @param MetadataPool $metadataPool
     * @param Wizard $resourcePage
     */
    public function __construct(
        MetadataPool $metadataPool,
        Wizard $resourcePage
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourcePage = $resourcePage;
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return object
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        $entityMetadata = $this->metadataPool->getMetadata(WizardInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $connection = $entityMetadata->getEntityConnection();

        $oldStores = $this->resourcePage->lookupStoreIds((int)$entity->getId());
        $newStores = (array)$entity->getStores();
        if (empty($newStores)) {
            $newStores = (array)$entity->getStoreId();
        }

        $table = $this->resourcePage->getTable('forix_wizard_store');

        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = [
                $linkField . ' = ?' => (int)$entity->getData($linkField),
                'store_id IN (?)' => $delete,
            ];
            $connection->delete($table, $where);
        }

        $insert = array_diff($newStores, $oldStores);
        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = [
                    $linkField => (int)$entity->getData($linkField),
                    'store_id' => (int)$storeId
                ];
            }
            $connection->insertMultiple($table, $data);
        }

        return $entity;
    }
}
