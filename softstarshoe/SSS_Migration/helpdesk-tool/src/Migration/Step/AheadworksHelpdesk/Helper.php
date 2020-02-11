<?php
namespace Migration\Step\AheadworksHelpdesk;

use Migration\ResourceModel\Destination;
use Migration\Reader\GroupsFactory;
use Migration\ResourceModel\Record;
use Migration\Exception;
use Migration\Handler\Manager as HandlerManager;
use Migration\Handler\ManagerFactory as HandlerManagerFactory;

/**
 * Class Helper
 */
class Helper
{
    /**
     * @var []
     */
    private $documentsDuplicateOnUpdate = [];

    /**
     * @var []
     */
    private $collectOldNewData = [];

    /**
     * @var []
     */
    private $tableToCollectOldNewData = [];

    /**
     * @var []
     */
    private $destinationCount;

    /**
     * @var Destination
     */
    private $destination;

    /**
     * @var HandlerManagerFactory
     */
    protected $handlerManagerFactory;

    /**
     * @param GroupsFactory $groupsFactory
     * @param Destination $destination
     * @param HandlerManagerFactory $handlerManagerFactory
     */
    public function __construct(
        GroupsFactory $groupsFactory,
        Destination $destination,
        HandlerManagerFactory $handlerManagerFactory
    ) {
        $this->readerGroups = $groupsFactory->create('aw_hdu3_groups_file');
        $this->destination = $destination;
        $this->handlerManagerFactory = $handlerManagerFactory;
        $this->documentsDuplicateOnUpdate = $this->readerGroups->getGroup('destination_documents_update_on_duplicate');
        $this->tableToCollectOldNewData = $this->readerGroups->getGroup('collect_old_new_data');
    }

    /**
     * Replace record data
     *
     * @param string $sourceDocName
     * @param Record $record
     * @return []|bool
     */
    public function replaceRecordData($sourceDocName, $record)
    {
        $tableSaveCollectedOldNewData = $this->readerGroups
            ->getGroup('save_collected_old_new_data_' . $sourceDocName);
        if ($tableSaveCollectedOldNewData) {
            // Replace old data on new
            foreach ($tableSaveCollectedOldNewData as $table => $field) {
                $oldValue = $record->getValue($field);
                $newValue = $oldValue;
                if (isset($this->collectOldNewData[$table])) {
                    if (isset($this->collectOldNewData[$table][$oldValue])) {
                        $newValue = $this->collectOldNewData[$table][$oldValue];
                    } else {
                        return false;
                    }
                }
                $record->setValue($field, $newValue);
            }
        }
        return $record;
    }

    /**
     * Save old new data
     *
     * @param string $sourceDocName
     * @param Record $record
     * @param int $insertId
     * @return null
     */
    public function saveOldNewData($sourceDocName, $record, $insertId)
    {
        if (isset($this->tableToCollectOldNewData[$sourceDocName])) {
            $oldValue = $record->getValue($this->tableToCollectOldNewData[$sourceDocName]);
            $this->collectOldNewData[$sourceDocName][$oldValue] = $insertId;
        }
    }

    /**
     * Get fields update on duplicate
     *
     * @param string $documentName
     * @return []|bool
     */
    public function getFieldsUpdateOnDuplicate($documentName)
    {
        $updateOnDuplicate = [];
        if (array_key_exists($documentName, $this->documentsDuplicateOnUpdate)) {
            $updateOnDuplicate = explode(',', $this->documentsDuplicateOnUpdate[$documentName]);
        }
        return $updateOnDuplicate;
    }

    /**
     * Set destination count for table name
     *
     * @param string $destinationName
     * @return null
     */
    public function setDestinationCount($destinationName)
    {
        $this->destinationCount[$destinationName] = $this->destination->getRecordsCount($destinationName);
    }

    /**
     * Get destination count for table name
     *
     * @param string $destinationName
     * @return null
     */
    public function getDestinationCount($destinationName)
    {
        return isset($this->destinationCount[$destinationName]) ? $this->destinationCount[$destinationName] : 0;
    }

    /**
     * Transform check event handler
     *
     * @param string $destDocName
     * @param Record $record
     * @return void
     * @throws Exception if handler is not valid
     */
    public function shouldBeTransformed($sourceDocName, $record)
    {
        $transformCheckData = $this->readerGroups
            ->getGroup('transform_check_' . $sourceDocName);
        if ($transformCheckData) {
            foreach ($transformCheckData as $key => $class) {
                if ($key != 'class') {
                    throw new Exception('XML file is invalid.');
                }
                /** @var HandlerManager $handlerManager */
                $handlerManager = $this->handlerManagerFactory->create();
                $handlerConfig = [
                    'class'     =>  $class,
                    'params'    => []
                ];
                $field = 'id';
                $handlerKey = md5($field . $handlerConfig['class']);
                $handlerManager->initHandler($field, $handlerConfig, $handlerKey);
                foreach ($handlerManager->getHandlers() as $handler) {
                    if (!$handler->handle($record, $record)) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * After save event handler
     *
     * @param string $destDocName
     * @param Record $record
     * @return void
     * @throws Exception if handler is not valid
     */
    public function afterSave($destDocName, $record)
    {
        $afterSaveData = $this->readerGroups
            ->getGroup('after_save_' . $destDocName);
        if ($afterSaveData) {
            foreach ($afterSaveData as $key => $class) {
                if ($key != 'class') {
                    throw new Exception('XML file is invalid.');
                }
                /** @var HandlerManager $handlerManager */
                $handlerManager = $this->handlerManagerFactory->create();
                $handlerConfig = [
                    'class'     =>  $class,
                    'params'    => []
                ];
                $field = 'id';
                $handlerKey = md5($field . $handlerConfig['class']);
                $handlerManager->initHandler($field, $handlerConfig, $handlerKey);
                foreach ($handlerManager->getHandlers() as $handler) {
                    $handler->handle($record, $record);
                }
            }
        }
    }
}
