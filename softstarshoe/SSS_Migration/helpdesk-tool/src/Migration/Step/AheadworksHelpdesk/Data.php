<?php
namespace Migration\Step\AheadworksHelpdesk;

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
use Migration\Config;
use Migration\ResourceModel\Document;

/**
 * Class Data
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
     * @var Helper
     */
    private $helper;

    /**
     * @var \Migration\Reader\Groups
     */
    private $readerGroups;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param ProgressBar\LogLevelProcessor $progress
     * @param ResourceModel\Source $source
     * @param ResourceModel\Destination $destination
     * @param ResourceModel\RecordFactory $recordFactory
     * @param \Migration\RecordTransformerFactory $recordTransformerFactory
     * @param MapFactory $mapFactory
     * @param GroupsFactory $groupsFactory
     * @param Logger $logger
     * @param Helper $helper
     * @param Config $config
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ProgressBar\LogLevelProcessor $progress,
        ResourceModel\Source $source,
        ResourceModel\Destination $destination,
        ResourceModel\RecordFactory $recordFactory,
        \Migration\RecordTransformerFactory $recordTransformerFactory,
        MapFactory $mapFactory,
        GroupsFactory $groupsFactory,
        Logger $logger,
        Helper $helper,
        Config $config
    ) {
        $this->source = $source;
        $this->destination = $destination;
        $this->recordFactory = $recordFactory;
        $this->recordTransformerFactory = $recordTransformerFactory;
        $this->map = $mapFactory->create('aw_hdu3_map_file');
        $this->progress = $progress;
        $this->readerGroups = $groupsFactory->create('aw_hdu3_groups_file');
        $this->logger = $logger;
        $this->helper = $helper;
        $this->config = $config;
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function perform()
    {
        $destinationAdapter = $this->destination->getAdapter()->getSelect()->getAdapter();
        $sourceAdapter = $this->source->getAdapter()->getSelect()->getAdapter();
        $sourceDocuments = array_keys($this->readerGroups->getGroup('source_documents'));
        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $destinationAdapter */
        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $sourceAdapter */
        $getFromTime = '2018-05-16 02:33:05';
        $lastTicketId = $sourceAdapter->fetchOne("SELECT min(id) 
                                    FROM  `aw_hdu3_ticket` 
                                    WHERE  `created_at` >=  '$getFromTime'");
        $this->source->setLastLoadedRecord('aw_hdu3_ticket',['id' => $lastTicketId]);

        $lastTicketMessageId = $sourceAdapter->fetchOne("SELECT min(ahtm.id) 
                                    FROM  `aw_hdu3_ticket_message`  AS `ahtm`
                                    INNER JOIN `aw_hdu3_ticket_history` AS `ahth`
                                    ON `ahtm`.`history_id` = `ahth`.`id`
                                    AND `ahth`.`created_at` >= '$getFromTime'");
        $this->source->setLastLoadedRecord('aw_hdu3_ticket_message',['id' => $lastTicketMessageId]);

        $lastTicketHistoryAttachmentId = $sourceAdapter->fetchOne("SELECT min(ahtha.id) 
                                    FROM  `aw_hdu3_ticket_history_attachment`  AS `ahtha`
                                    INNER JOIN `aw_hdu3_ticket_history` AS `ahth`
                                    ON `ahtha`.`ticket_history_id` = `ahth`.`id`
                                    AND `ahth`.`created_at` >= '$getFromTime'");
        $this->source->setLastLoadedRecord('aw_hdu3_ticket_history_attachment',['id' => $lastTicketHistoryAttachmentId]);

        $this->progress->start(count($sourceDocuments), LogManager::LOG_LEVEL_INFO);
        foreach ($sourceDocuments as $sourceDocName) {
            $sourceDocument = $this->source->getDocument($sourceDocName);
            $destinationName = $this->map->getDocumentMap($sourceDocName, MapInterface::TYPE_SOURCE);
            if (!$destinationName) {
                continue;
            }
            $destDocument = $this->destination->getDocument($destinationName);
            $this->helper->setDestinationCount($destinationName);
            $this->logger->debug('migrating', ['table' => $sourceDocName]);
            $recordTransformer = $this->getRecordTransformer($sourceDocument, $destDocument);
            $pageNumber = 0;
            $this->progress->start(
                ceil($this->source->getRecordsCount($sourceDocName) / $this->source->getPageSize($sourceDocName)),
                LogManager::LOG_LEVEL_DEBUG
            );
            while (!empty($items = $this->source->getRecords($sourceDocName, $pageNumber))) {
                $pageNumber++;
                foreach ($items as $recordData) {
                    $this->source->setLastLoadedRecord($sourceDocName, $recordData);
                    /** @var Record $record */
                    $record = $this->recordFactory->create(['document' => $sourceDocument, 'data' => $recordData]);
                    /** @var Record $destRecord */
                    $destRecord = $this->recordFactory->create(['document' => $destDocument]);

                    if (!$record = $this->helper->replaceRecordData($sourceDocName, $record)) {
                        continue;
                    }

                    if (!$this->helper->shouldBeTransformed($sourceDocName, $record)) {
                        continue;
                    }

                    $recordTransformer->transform($record, $destRecord);

                    if (!$destRecord = $this->helper->replaceRecordData($destinationName, $destRecord)) {
                        continue;
                    }
                    if ($destRecord->getValue('id')) {
                        $destRecord->setValue('id', null);
                    }
                    $this->progress->advance(LogManager::LOG_LEVEL_DEBUG);
                    $fieldsUpdateOnDuplicate = $this->helper->getFieldsUpdateOnDuplicate($destinationName);
                    $destinationAdapter->insertOnDuplicate(
                        $this->destination->addDocumentPrefix($destinationName),
                        $destRecord->getData(),
                        $fieldsUpdateOnDuplicate
                    );
                    $insertId = $destinationAdapter->lastInsertId();

                    if (!$destRecord->getValue('id')) {
                        $destRecord->setValue('id', $insertId);
                    }
                    $this->helper->afterSave($destinationName, $destRecord);

                    $this->helper->saveOldNewData($sourceDocName, $record, $insertId);
                }
            }
            $this->source->setLastLoadedRecord($sourceDocName, []);
            $this->progress->advance(LogManager::LOG_LEVEL_INFO);
            $this->progress->finish(LogManager::LOG_LEVEL_DEBUG);
        }
        $this->progress->finish(LogManager::LOG_LEVEL_INFO);
        return true;
    }

    /**
     * Get record transformer
     *
     * @param Document $sourceDocument
     * @param Document $destDocument
     * @return \Migration\RecordTransformer
     */
    public function getRecordTransformer(Document $sourceDocument, Document $destDocument)
    {
        /** @var \Migration\RecordTransformer $recordTransformer */
        $recordTransformer = $this->recordTransformerFactory->create(
            [
                'sourceDocument' => $sourceDocument,
                'destDocument' => $destDocument,
                'mapReader' => $this->map
            ]
        );
        $recordTransformer->init();
        return $recordTransformer;
    }
}
