<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Setup;

use Aheadworks\Giftcard\Model\Giftcard\History\CommentInterface;
use Aheadworks\Giftcard\Model\Source\EmailStatus;
use Aheadworks\Giftcard\Model\Source\Giftcard\Status;
use Aheadworks\Giftcard\Model\Source\YesNo;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Aheadworks\Giftcard\Model\Source\History\Comment\Action as SourceHistoryCommentAction;
use Aheadworks\Giftcard\Model\Source\History\EntityType as SourceHistoryEntityType;
use Aheadworks\Giftcard\Model\Giftcard\History\CommentPool;
use Magento\Framework\EntityManager\EntityManager;
use Aheadworks\Giftcard\Api\Data\Giftcard\History\EntityInterface as HistoryEntityInterface;
use Aheadworks\Giftcard\Api\Data\Giftcard\History\EntityInterfaceFactory as HistoryEntityInterfaceFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Framework\App\State;
use Magento\Framework\App\Area;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Giftcard\Model\Source\History\Action as HistoryAction;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class UpgradeSchema
 *
 * @package Aheadworks\Giftcard\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var CommentPool
     */
    private $commentPool;

    /**
     * @var HistoryEntityInterfaceFactory
     */
    private $historyEntityFactory;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var CreditmemoRepositoryInterface
     */
    private $creditmemoRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param State $appState
     * @param CommentPool $commentPool
     * @param HistoryEntityInterfaceFactory $historyEntityFactory
     * @param EntityManager $entityManager
     * @param OrderRepositoryInterface $orderRepository
     * @param CreditmemoRepositoryInterface $creditmemoRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        State $appState,
        CommentPool $commentPool,
        HistoryEntityInterfaceFactory $historyEntityFactory,
        EntityManager $entityManager,
        OrderRepositoryInterface $orderRepository,
        CreditmemoRepositoryInterface $creditmemoRepository,
        StoreManagerInterface $storeManager
    ) {
        try {
            $appState->setAreaCode(Area::AREA_ADMINHTML);
        } catch (LocalizedException $e) {
        }
        $this->commentPool = $commentPool;
        $this->historyEntityFactory = $historyEntityFactory;
        $this->entityManager = $entityManager;
        $this->orderRepository = $orderRepository;
        $this->creditmemoRepository = $creditmemoRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if ($context->getVersion() && version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->addTables110($setup);
            $this->updateTableData110($setup);
        }
        if ($context->getVersion() && version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->addTables120($setup);
        }
    }

    /**
     * Add tables for version 1.1.0
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addTables110(SchemaSetupInterface $installer)
    {
        /**
         * Create table 'aw_giftcard_order'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_giftcard_order'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'giftcard_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Giftcard Id'
            )
            ->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Order Id'
            )
            ->addColumn(
                'base_giftcard_amount',
                Table::TYPE_DECIMAL,
                '12,2',
                ['unsigned' => true, 'default' => null],
                'Base Giftcard Amount'
            )
            ->addColumn(
                'giftcard_amount',
                Table::TYPE_DECIMAL,
                '12,2',
                ['unsigned' => true, 'default' => null],
                'Giftcard Amount'
            )
            ->addIndex(
                $installer->getIdxName('aw_giftcard_order', ['giftcard_id']),
                ['giftcard_id']
            )
            ->setComment('Giftcard To Order Linkage Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_giftcard_history_entity'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_giftcard_history_entity'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'history_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true],
                'History Id'
            )->addColumn(
                'entity_type',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Entity Type'
            )->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Entity Id'
            )->addColumn(
                'entity_label',
                Table::TYPE_TEXT,
                '255',
                ['nullable' => true],
                'Entity Label'
            )->addIndex(
                $installer->getIdxName('aw_giftcard_history_entity', ['history_id', 'entity_type', 'entity_id']),
                ['history_id', 'entity_type', 'entity_id']
            )->addForeignKey(
                $installer->getFkName(
                    'aw_giftcard_history_entity',
                    'history_id',
                    'aw_giftcard_history',
                    'id'
                ),
                'history_id',
                $installer->getTable('aw_giftcard_history'),
                'id',
                Table::ACTION_CASCADE
            )->setComment('Aheadworks Giftcard History Entity');
        $installer->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Update table data for version 1.1.0
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function updateTableData110(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        // Remove is_used column
        if ($connection->tableColumnExists($installer->getTable('aw_giftcard'), 'is_used')) {
            $connection->update(
                $installer->getTable('aw_giftcard'),
                ['state' => Status::ACTIVE],
                'is_used = 2' // 2 - Partially Used
            );
            $connection->dropColumn($installer->getTable('aw_giftcard'), 'is_used');
        }

        // Change {{var card_image_base_url}} to {{var card_image_base_url|raw}}
        $connection->update(
            $installer->getTable('email_template'),
            ['template_text' => new \Zend_Db_Expr(
                'REPLACE(`template_text`, "{{var card_image_base_url}}", "{{var card_image_base_url|raw}}")'
            )]
        );

        // Change store_id
        $connection->update(
            $installer->getTable('aw_giftcard_statistics'),
            ['store_id' => $this->storeManager->getDefaultStoreView()->getStoreId()],
            'store_id = 0'
        );

        // Add columns to giftcard table
        $connection->addColumn(
            $installer->getTable('aw_giftcard'),
            'delivery_date',
            [
                'type' => Table::TYPE_DATETIME,
                'nullable' => true,
                'comment' => 'Delivery Date'
            ]
        );
        $connection->addColumn(
            $installer->getTable('aw_giftcard'),
            'delivery_date_timezone',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'length' => 255,
                'comment' => 'Delivery Date Timezone'
            ]
        );
        $connection->addColumn(
            $installer->getTable('aw_giftcard'),
            'email_sent',
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => EmailStatus::SENT,
                'comment' => 'Email Sent'
            ]
        );
        $connection->addColumn(
            $installer->getTable('aw_giftcard'),
            'headline',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Headline'
            ]
        );
        $connection->addColumn(
            $installer->getTable('aw_giftcard'),
            'message',
            [
                'type' => Table::TYPE_TEXT,
                'length' => '2M',
                'nullable' => true,
                'comment' => 'Message'
            ]
        );

        // Change additional information in history table
        $connection->addColumn(
            $installer->getTable('aw_giftcard_history'),
            'comment',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'length' => 255,
                'comment' => 'Comment'
            ]
        );
        $connection->addColumn(
            $installer->getTable('aw_giftcard_history'),
            'comment_placeholder',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'length' => 255,
                'comment' => 'Comment Placeholder'
            ]
        );
        $connection->addColumn(
            $installer->getTable('aw_giftcard_history'),
            'action_type',
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => false,
                'comment' => 'Comment Action Type'
            ]
        );

        if ($connection->tableColumnExists($installer->getTable('aw_giftcard_history'), 'additional_info')) {
            $select = $connection->select()
                ->from($installer->getTable('aw_giftcard_history'));
            $histories = $connection->fetchAssoc($select);

            // Convert comment
            foreach ($histories as $history) {
                if (!$history['additional_info']) {
                    continue;
                }
                $additionalInfo = unserialize($history['additional_info']);
                $historyData = [
                    'comment' => '',
                    'comment_placeholder' => '',
                    'action_type' => ''
                ];
                $historyEntityObjects = [];
                switch ($additionalInfo['message_type']) {
                    case 0: // BY_ADMIN_MESSAGE_VALUE
                        $historyData['action_type'] = SourceHistoryCommentAction::BY_ADMIN;

                        /** @var HistoryEntityInterface $historyEntityObject */
                        $historyEntityObject = $this->historyEntityFactory->create();
                        $historyEntityObject
                            ->setHistoryId($history['id'])
                            ->setEntityType(SourceHistoryEntityType::ADMIN_ID)
                            ->setEntityId(0)
                            ->setEntityLabel($additionalInfo['message_data']);
                        $historyEntityObjects = [$historyEntityObject];
                        break;
                    case 1: // BY_ORDER_MESSAGE_VALUE
                        try {
                            $orderIncrement = $this->orderRepository
                                ->get($additionalInfo['message_data'])
                                ->getIncrementId();
                        } catch (NoSuchEntityException $e) {
                            $orderIncrement = $additionalInfo['message_data'];
                        }

                        if ($history['balance_delta'] < 0) {
                            $historyData['action_type'] = SourceHistoryCommentAction::REIMBURSED_FOR_CANCELLED_ORDER;
                        } else {
                            $historyData['action_type'] = SourceHistoryCommentAction::CREATED_BY_ORDER;
                        }

                        /** @var HistoryEntityInterface $historyEntityObject */
                        $historyEntityObject = $this->historyEntityFactory->create();
                        $historyEntityObject
                            ->setHistoryId($history['id'])
                            ->setEntityType(SourceHistoryEntityType::ORDER_ID)
                            ->setEntityId($additionalInfo['message_data'])
                            ->setEntityLabel($orderIncrement);
                        $historyEntityObjects = [$historyEntityObject];
                        break;
                    case 2: // BY_CREDITMEMO_MESSAGE_VALUE
                        try {
                            $creditmemoIncrement = $this->creditmemoRepository
                                ->get($additionalInfo['message_data'])
                                ->getIncrementId();
                        } catch (NoSuchEntityException $e) {
                            $creditmemoIncrement = $additionalInfo['message_data'];
                        }

                        $historyData['action_type'] = SourceHistoryCommentAction::REFUND_GIFTCARD;
                        /** @var HistoryEntityInterface $historyOrderObject */
                        $historyOrderObject = $this->historyEntityFactory->create();
                        $historyOrderObject
                            ->setHistoryId($history['id'])
                            ->setEntityType(SourceHistoryEntityType::ORDER_ID)
                            ->setEntityId(0)
                            ->setEntityLabel(0);
                        /** @var HistoryEntityInterface $historyCreditmemoObject */
                        $historyCreditmemoObject = $this->historyEntityFactory->create();
                        $historyCreditmemoObject
                            ->setHistoryId($history['id'])
                            ->setEntityType(SourceHistoryEntityType::CREDIT_MEMO_ID)
                            ->setEntityId($additionalInfo['message_data'])
                            ->setEntityLabel($creditmemoIncrement);

                        $historyEntityObjects = [$historyOrderObject, $historyCreditmemoObject];
                        break;
                }
                /** @var CommentInterface $commentRender */
                $commentRender = $this->commentPool->get($historyData['action_type']);
                $historyData['comment'] = $commentRender->renderComment($historyEntityObjects);
                $historyData['comment_placeholder'] = $commentRender->getLabel();

                foreach ($historyEntityObjects as $historyEntityObject) {
                    $this->entityManager->save($historyEntityObject);
                }
                $connection->update(
                    $installer->getTable('aw_giftcard_history'),
                    $historyData,
                    'id = ' . $history['id']
                );
            }
            $connection->dropColumn($installer->getTable('aw_giftcard_history'), 'additional_info');
        }
        $connection->update(
            $installer->getTable('aw_giftcard_history'),
            ['action' => HistoryAction::UPDATED],
            'action = 4' // PARTIALLY_USED
        );

        return $this;
    }

    /**
     * Add tables for version 1.2.0
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addTables120(SchemaSetupInterface $installer)
    {
        /**
         * Create table 'aw_giftcard_pool'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_giftcard_pool'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                100,
                ['nullable' => false],
                'Pool Name'
            )->addColumn(
                'code_length',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Code Length'
            )->addColumn(
                'code_format',
                Table::TYPE_TEXT,
                30,
                ['nullable' => false],
                'Code Format'
            )->addColumn(
                'code_prefix',
                Table::TYPE_TEXT,
                10,
                ['nullable' => true],
                'Code Prefix'
            )->addColumn(
                'code_suffix',
                Table::TYPE_TEXT,
                10,
                ['nullable' => true],
                'Code Suffix'
            )->addColumn(
                'code_delimiter_at_every',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Code Delimiter Every X Characters'
            )->addIndex(
                $installer->getIdxName('aw_giftcard_pool', ['id']),
                ['id']
            )->addIndex(
                $installer->getIdxName('aw_giftcard_pool', ['name']),
                ['name']
            )->setComment('Giftcard Pool Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_giftcard_pool_code'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_giftcard_pool_code'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'pool_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Pool Id'
            )->addColumn(
                'code',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Code'
            )->addColumn(
                'used',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => YesNo::NO],
                'Used'
            )->addIndex(
                $installer->getIdxName('aw_giftcard_pool_code', ['id']),
                ['id']
            )->addIndex(
                $installer->getIdxName('aw_giftcard_pool_code', ['code']),
                ['code']
            )->addForeignKey(
                $installer->getFkName('aw_giftcard_pool', 'id', 'aw_giftcard_pool_code', 'pool_id'),
                'pool_id',
                $installer->getTable('aw_giftcard_pool'),
                'id',
                Table::ACTION_CASCADE
            )->setComment('Giftcard Pool Codes Table');
        $installer->getConnection()->createTable($table);
    }
}
