<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\ResourceModel\Giftcard\History;

use Aheadworks\Giftcard\Model\Giftcard\History;
use Aheadworks\Giftcard\Model\ResourceModel\Giftcard\History as ResourceHistory;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Aheadworks\Giftcard\Api\Data\Giftcard\History\EntityInterface as HistoryEntityInterface;
use Aheadworks\Giftcard\Api\Data\Giftcard\History\EntityInterfaceFactory as HistoryEntityInterfaceFactory;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Collection
 *
 * @package Aheadworks\Giftcard\Model\ResourceModel\History
 */
class Collection extends AbstractCollection
{
    /**
     * @var HistoryEntityInterfaceFactory
     */
    private $historyEntityFactory;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param HistoryEntityInterfaceFactory $historyEntityFactory
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        HistoryEntityInterfaceFactory $historyEntityFactory,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->historyEntityFactory = $historyEntityFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(History::class, ResourceHistory::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->joinLeft(
            ['awgc_table' => $this->getTable('aw_giftcard')],
            'main_table.giftcard_id = awgc_table.id',
            ['website_id']
        );
        return $this;
    }

    /**
     * Add filter on gift card id
     *
     * @param int $giftcardId
     * @return $this
     */
    public function addGiftcardFilter($giftcardId)
    {
        $this->addFieldToFilter('giftcard_id', $giftcardId);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        $this->attachRelationTable(
            'aw_giftcard_history_entity',
            'id',
            'history_id',
            'entities'
        );
        return parent::_afterLoad();
    }

    /**
     * Attach entity table data to collection items
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $linkageColumnName
     * @param string $fieldName
     * @return void
     */
    private function attachRelationTable(
        $tableName,
        $columnName,
        $linkageColumnName,
        $fieldName
    ) {
        $ids = $this->getColumnValues($columnName);
        if (count($ids)) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from([$tableName . '_table' => $this->getTable($tableName)])
                ->where($tableName . '_table.' . $linkageColumnName . ' IN (?)', $ids);

            /** @var \Magento\Framework\DataObject $item */
            foreach ($this as $item) {
                $result = [];
                $id = $item->getData($columnName);
                foreach ($connection->fetchAll($select) as $data) {
                    if ($data[$linkageColumnName] == $id) {
                        /** @var HistoryEntityInterface $adminHistoryEntityObject */
                        $adminHistoryEntityObject = $this->historyEntityFactory->create();
                        $adminHistoryEntityObject
                            ->setEntityType($data['entity_type'])
                            ->setEntityId($data['entity_id'])
                            ->setEntityLabel($data['entity_label']);
                        $result[] = $adminHistoryEntityObject;
                    }
                }
                $item->setData($fieldName, $result);
            }
        }
    }
}
