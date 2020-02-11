<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Migration\Step\Customer;

use Migration\App\Step\StageInterface;
use Migration\Reader\MapInterface;
use Migration\Reader\GroupsFactory;
use Migration\Reader\Map;
use Migration\Reader\MapFactory;
use Migration\ResourceModel;
use Migration\ResourceModel\Record;
use Migration\App\ProgressBar;
use Migration\Logger\Manager as LogManager;
use Migration\Logger\Logger;
use Migration\Step\Customer\Model;

/**
 * Class Data modified for Blauer Project
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Migration\Step\DatabaseStage implements StageInterface
{
    /**
     * @var ResourceModel\Source
     */
    private $source;

    /**
     * @var ResourceModel\Destination
     */
    private $destination;

    /**
     * @var ResourceModel\RecordFactory
     */
    private $recordFactory;

    /**
     * @var Map
     */
    private $map;

    /**
     * @var \Migration\RecordTransformerFactory
     */
    private $recordTransformerFactory;

    /**
     * @var ProgressBar\LogLevelProcessor
     */
    private $progress;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var \Migration\Reader\Groups
     */
    private $readerGroups;

    /**
     * @var Model\AttributesDataToCustomerEntityRecords
     */
    private $attributesDataToCustomerEntityRecords;

    /**
     * @var Model\AttributesDataToSkip
     */
    private $attributesDataToSkip;

    /**
     * @var Model\AttributesToStatic
     */
    private $attributesToStatic;

    /**
     * @var array
     */
    private $documentAttributeTypes;

    /**
     * @var array
     */
    private $eavAttributesSource;

    /**
     * @var array
     */
    private $eavAttributesDest;

    /**
     * @var array
     */
    private $mapAttributes;

    /**
     * @param \Migration\Config $config
     * @param ProgressBar\LogLevelProcessor $progress
     * @param ResourceModel\Source $source
     * @param ResourceModel\Destination $destination
     * @param ResourceModel\RecordFactory $recordFactory
     * @param \Migration\RecordTransformerFactory $recordTransformerFactory
     * @param Model\AttributesDataToCustomerEntityRecords $attributesDataToCustomerEntityRecords
     * @param Model\AttributesDataToSkip $attributesDataToSkip
     * @param Model\AttributesToStatic $attributesToStatic
     * @param MapFactory $mapFactory
     * @param GroupsFactory $groupsFactory
     * @param Logger $logger
     * @param Helper $helper
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Migration\Config $config,
        ProgressBar\LogLevelProcessor $progress,
        ResourceModel\Source $source,
        ResourceModel\Destination $destination,
        ResourceModel\RecordFactory $recordFactory,
        \Migration\RecordTransformerFactory $recordTransformerFactory,
        Model\AttributesDataToCustomerEntityRecords $attributesDataToCustomerEntityRecords,
        Model\AttributesDataToSkip $attributesDataToSkip,
        Model\AttributesToStatic $attributesToStatic,
        MapFactory $mapFactory,
        GroupsFactory $groupsFactory,
        Logger $logger
    ) {
        $this->source = $source;
        $this->destination = $destination;
        $this->recordFactory = $recordFactory;
        $this->recordTransformerFactory = $recordTransformerFactory;
        $this->map = $mapFactory->create('customer_map_file');
        $this->progress = $progress;
        $this->readerGroups = $groupsFactory->create('customer_document_groups_file');
        $this->logger = $logger;
        $this->attributesDataToCustomerEntityRecords = $attributesDataToCustomerEntityRecords;
        $this->attributesDataToSkip = $attributesDataToSkip;
        $this->attributesToStatic = $attributesToStatic;
        parent::__construct($config);
    }

    /**
     * @return bool
     */
    public function perform()
    {
        $sourceDocuments = array_keys($this->readerGroups->getGroup('source_documents'));
        $this->progress->start(count($sourceDocuments), LogManager::LOG_LEVEL_INFO);
        /**
         * Before migrate
         */
        /** @var \Migration\ResourceModel\Adapter\Pdo\MysqlBuilder $destAdapter */
        $destAdapter = \Magento\Framework\App\ObjectManager::getInstance()->create('Migration\ResourceModel\Adapter\Pdo\MysqlBuilder');
        $destAdapter = $destAdapter->build('destination');
        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $destAdapter */
        try {
            $destAdapter->dropIndex('customer_entity', 'CUSTOMER_ENTITY_EMAIL_WEBSITE_ID');
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }
        $this->migrateCustomerEntities();
        $this->migrateCustomerData();
        try {
            $destAdapter->query('SET FOREIGN_KEY_CHECKS=0');
            $destAdapter->update('customer_entity', ['website_id' => 1, 'store_id' => 1]);
            $destAdapter->createTemporaryTableLike('customer_entity_migrate_tmp', 'customer_entity');
            $customerSelect = $destAdapter->select()->from('customer_entity', '*');
            $destAdapter->query($destAdapter->insertFromSelect($customerSelect, 'customer_entity_migrate_tmp'));
            $destAdapter->truncateTable('customer_entity');
            $destAdapter->addIndex('customer_entity', 'CUSTOMER_ENTITY_EMAIL_WEBSITE_ID', ['email', 'website_id'], 'unique');
            $customerSelectTmp = $destAdapter->select()->from('customer_entity_migrate_tmp', '*');
            $destAdapter->query($destAdapter->insertFromSelect($customerSelectTmp, 'customer_entity', [], 2));
            $destAdapter->delete('customer_entity_varchar', '`value` = \'\'');
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }
        $this->progress->finish(LogManager::LOG_LEVEL_INFO);
        return true;
    }

    /**
     * Migrate given document to the destination
     * with possibility of excluding some of the records
     *
     * @param $sourceDocName
     * @param array|null $attributesToSkip
     * @throws \Migration\Exception
     */
    private function transformDocumentRecords(
        $sourceDocName,
        array $attributesToSkip = null
    ) {
        $sourceEntityDocuments = array_keys($this->readerGroups->getGroup('source_entity_documents'));
        $sourceDocument = $this->source->getDocument($sourceDocName);
        $destinationName = $this->map->getDocumentMap($sourceDocName, MapInterface::TYPE_SOURCE);
        if (!$destinationName) {
            return;
        }
        $destDocument = $this->destination->getDocument($destinationName);
        $this->destination->clearDocument($destinationName);

        /**
         * End before migrate
         */
        /** @var \Migration\RecordTransformer $recordTransformer */
        $recordTransformer = $this->recordTransformerFactory->create(
            [
                'sourceDocument' => $sourceDocument,
                'destDocument' => $destDocument,
                'mapReader' => $this->map
            ]
        );
        $recordTransformer->init();
        $pageNumber = 0;
        $this->logger->debug('migrating', ['table' => $sourceDocName]);
        $this->progress->start(
            ceil($this->source->getRecordsCount($sourceDocName) / $this->source->getPageSize($sourceDocName)),
            LogManager::LOG_LEVEL_DEBUG
        );
        while (!empty($bulk = $this->source->getRecords($sourceDocName, $pageNumber))) {
            $pageNumber++;
            $destinationRecords = $destDocument->getRecords();
            foreach ($bulk as $recordData) {
                if ($attributesToSkip !== null
                    && isset($recordData['attribute_id'])
                    && in_array($recordData['attribute_id'], $attributesToSkip)
                ) {
                    continue;
                }
                $recordData = $this->mapAttributesData($recordData, $sourceDocName);
                /** @var Record $record */
                $record = $this->recordFactory->create(['document' => $sourceDocument, 'data' => $recordData]);
                /** @var Record $destRecord */
                $destRecord = $this->recordFactory->create(['document' => $destDocument]);
                $recordTransformer->transform($record, $destRecord);
                $destinationRecords->addRecord($destRecord);
            }
            if (in_array($sourceDocName, $sourceEntityDocuments)) {
                $this->attributesDataToCustomerEntityRecords
                    ->updateCustomerEntities($sourceDocName, $destinationRecords);
            }
            $this->source->setLastLoadedRecord($sourceDocName, end($bulk));
            $this->progress->advance(LogManager::LOG_LEVEL_DEBUG);
            $this->destination->saveRecords($destinationName, $destinationRecords);
        }
        $this->progress->advance(LogManager::LOG_LEVEL_INFO);
        $this->progress->finish(LogManager::LOG_LEVEL_DEBUG);
    }


    /**
     * @param $recordData
     * @param $sourceDocName
     * @return mixed
     */
    private function mapAttributesData($recordData, $sourceDocName)
    {
        if (isset($recordData['attribute_id'])) {
            try {
                $maps = $this->mapAttributes($sourceDocName);
                if (!empty($maps[$recordData['attribute_id']])) {
                    $recordData['attribute_id'] = $maps[$recordData['attribute_id']];
                }
            } catch (\Exception $exception) {
                return $recordData;
            }
        }

        return $recordData;
    }

    /**
     * @param $sourceDocName
     * @return mixed|null
     */
    private function mapAttributes($sourceDocName)
    {
        if (empty($this->mapAttributes[$sourceDocName])) {

            /**
             * Mapping Attribute for Blauer
             */
            $mapsAttributeCode = [
                'supersize' => 'is_supersize',
                'oversize' => 'is_oversize',
                'nav_number' => 'navision_code'
            ];
            $attributesSource = $this->getEavAttributesSource($sourceDocName);
            $attributesDest = $this->getEavAttributesDest($sourceDocName);
            foreach ($attributesSource as $attributeId => $attributeCode) {
                if (isset($mapsAttributeCode[$attributeCode])) {
                    $attributeCode = $mapsAttributeCode[$attributeCode];
                }
                $newId = array_search($attributeCode, $attributesDest);
                if (array_search($attributeCode, $attributesDest) !== false) {
                    $this->mapAttributes[$sourceDocName][$attributeId] = $newId;
                }
            }
        }

        return $this->mapAttributes[$sourceDocName] ? $this->mapAttributes[$sourceDocName] : null;
    }

    /**
     * @param $sourceDocName
     * @return mixed
     */
    protected function getEavAttributesSource($sourceDocName)
    {
        $entityTypeCode = $this->getEntityTypeCodeByDocumentName($sourceDocName);
        if (!isset($this->eavAttributesSource[$entityTypeCode])) {
            $this->eavAttributesSource[$entityTypeCode] = $this->getAttributesData($this->source->getAdapter(), $entityTypeCode);
        };

        return $this->eavAttributesSource[$entityTypeCode];
    }

    /**
     * @param $sourceDocName
     * @return mixed
     */
    protected function getEavAttributesDest($sourceDocName)
    {
        $entityTypeCode = $this->getEntityTypeCodeByDocumentName($sourceDocName);
        if (!isset($this->eavAttributesDest[$entityTypeCode])) {
            $this->eavAttributesDest[$entityTypeCode] = $this->getAttributesData($this->destination->getAdapter(), $entityTypeCode);
        };

        return $this->eavAttributesDest[$entityTypeCode];
    }

    /**
     * @param \Migration\ResourceModel\AdapterInterface $adapter
     * @param $sourceDocName
     */
    private function getAttributesData($adapter, $entityTypeCode)
    {
        /** @var \Magento\Framework\DB\Select $query */
        $query = $adapter->getSelect()
            ->from(
                ['et' => $this->source->addDocumentPrefix('eav_entity_type')],
                []
            )->join(
                ['ea' => $this->source->addDocumentPrefix('eav_attribute')],
                'et.entity_type_id = ea.entity_type_id',
                [
                    'attribute_id',
                    'attribute_code',
                ]
            )->where(
                'et.entity_type_code = ?',
                $entityTypeCode
            );

        $attributes = $query->getAdapter()->fetchPairs($query);

        return $attributes;
    }

    /**
     * Retrieves entity type code by document name
     *
     * @param string $sourceDocName
     * @return string|null
     */
    public function getEntityTypeCodeByDocumentName($sourceDocName)
    {
        if (empty($this->documentAttributeTypes)) {
            $entityTypeCodes = array_keys($this->readerGroups->getGroup('eav_entities'));
            foreach ($entityTypeCodes as $entityTypeCode) {
                $documents = $this->readerGroups->getGroup($entityTypeCode);
                $documents = array_keys($documents);
                foreach ($documents as $documentName) {
                    $this->documentAttributeTypes[$documentName] = $entityTypeCode;
                }
            }
        }
        return isset($this->documentAttributeTypes[$sourceDocName]) ?
            $this->documentAttributeTypes[$sourceDocName] :
            null;
    }

    /**
     * Migrate customer entity tables
     */
    private function migrateCustomerEntities()
    {
        $sourceEntityDocuments = array_keys($this->readerGroups->getGroup('source_entity_documents'));
        foreach ($sourceEntityDocuments as $sourceEntityDocument) {
            $this->transformDocumentRecords($sourceEntityDocument);
        }
        $this->attributesToStatic->update();
    }

    /**
     * Migrate data of customers
     */
    private function migrateCustomerData()
    {
        $skippedAttributes = array_keys($this->attributesDataToSkip->getSkippedAttributes());
        $sourceDocuments = array_keys($this->readerGroups->getGroup('source_documents'));
        $sourceEntityDocuments = array_keys($this->readerGroups->getGroup('source_entity_documents'));
        $sourceDataDocuments = array_diff($sourceDocuments, $sourceEntityDocuments);
        foreach ($sourceDataDocuments as $sourceDataDocument) {
            $this->transformDocumentRecords($sourceDataDocument, $skippedAttributes);
        }
    }
}
