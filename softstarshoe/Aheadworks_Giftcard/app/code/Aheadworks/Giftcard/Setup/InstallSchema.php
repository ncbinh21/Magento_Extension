<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Setup;

use Aheadworks\Giftcard\Model\Source\EmailStatus;
use Aheadworks\Giftcard\Model\Source\YesNo;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'aw_giftcard'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_giftcard'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Giftcard Id'
            )
            ->addColumn(
                'code',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Giftcard Code'
            )
            ->addColumn(
                'type',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Giftcard Type'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At'
            )
            ->addColumn(
                'expire_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true, 'default' => null],
                'Expired At'
            )
            ->addColumn(
                'website_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Website Id'
            )
            ->addColumn(
                'balance',
                Table::TYPE_DECIMAL,
                '12,2',
                ['nullable' => false, 'default' => '0.00'],
                'Balance'
            )
            ->addColumn(
                'initial_balance',
                Table::TYPE_DECIMAL,
                '12,2',
                ['nullable' => false, 'default' => '0.00'],
                'Initial Balance'
            )
            ->addColumn(
                'state',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'State'
            )
            ->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                'Order ID'
            )
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Product ID'
            )
            ->addColumn(
                'email_template',
                Table::TYPE_TEXT,
                255,
                ['default' => '0'],
                'Email Template'
            )
            ->addColumn(
                'sender_name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Sender Name'
            )
            ->addColumn(
                'sender_email',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Sender Email'
            )
            ->addColumn(
                'recipient_name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Recipient Name'
            )
            ->addColumn(
                'recipient_email',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Recipient Email'
            )
            ->addColumn(
                'delivery_date',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'Delivery Date'
            )
            ->addColumn(
                'delivery_date_timezone',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Delivery Date Timezone'
            )
            ->addColumn(
                'email_sent',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => EmailStatus::SENT],
                'Email Sent'
            )
            ->addColumn(
                'headline',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Headline'
            )
            ->addColumn(
                'message',
                Table::TYPE_TEXT,
                '2M',
                ['nullable' => true],
                'Message'
            )
            ->addIndex(
                $installer->getIdxName('aw_giftcard', ['website_id']),
                ['website_id']
            )
            ->addForeignKey(
                $installer->getFkName('aw_giftcard', 'website_id', 'store_website', 'website_id'),
                'website_id',
                $installer->getTable('store_website'),
                'website_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Giftcard');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_giftcard_history'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_giftcard_history'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'History Id'
            )
            ->addColumn(
                'giftcard_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Giftcard Id'
            )
            ->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Updated At'
            )
            ->addColumn(
                'action',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Action'
            )
            ->addColumn(
                'balance_amount',
                Table::TYPE_DECIMAL,
                '12,2',
                ['nullable' => false, 'default' => '0.00'],
                'Balance Amount'
            )
            ->addColumn(
                'balance_delta',
                Table::TYPE_DECIMAL,
                '12,2',
                ['nullable' => false, 'default' => '0.00'],
                'Balance Delta'
            )
            ->addColumn(
                'comment',
                Table::TYPE_TEXT,
                255,
                [],
                'Comment'
            )
            ->addColumn(
                'comment_placeholder',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Comment Placeholder'
            )
            ->addColumn(
                'action_type',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Comment Action Type'
            )
            ->addIndex(
                $installer->getIdxName('aw_giftcard_history', ['giftcard_id']),
                ['giftcard_id']
            )
            ->addForeignKey(
                $installer->getFkName('aw_giftcard_history', 'giftcard_id', 'aw_giftcard', 'id'),
                'giftcard_id',
                $installer->getTable('aw_giftcard'),
                'id',
                Table::ACTION_CASCADE
            )
            ->setComment('Giftcard History');
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

        /**
         * Create table 'aw_giftcard_quote'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_giftcard_quote'))
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
                'quote_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Quote Id'
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
                $installer->getIdxName('aw_giftcard_quote', ['giftcard_id']),
                ['giftcard_id']
            )
            ->setComment('Giftcard To Quote Linkage Table');
        $installer->getConnection()->createTable($table);

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
         * Create table 'aw_giftcard_invoice'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_giftcard_invoice'))
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
                'invoice_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Invoice Id'
            )
            ->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                'Order ID'
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
                $installer->getIdxName('aw_giftcard_invoice', ['giftcard_id']),
                ['giftcard_id']
            )
            ->setComment('Giftcard To Invoice Linkage Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_giftcard_creditmemo'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_giftcard_creditmemo'))
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
                'creditmemo_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Creditmemo Id'
            )
            ->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                'Order ID'
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
                $installer->getIdxName('aw_giftcard_creditmemo', ['giftcard_id']),
                ['giftcard_id']
            )
            ->setComment('Giftcard To Creditmemo Linkage Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_giftcard_product_entity_amounts'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_giftcard_product_entity_amounts'))
            ->addColumn(
                'value_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ValueId'
            )
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Entity ID'
            )
            ->addColumn(
                'value',
                Table::TYPE_DECIMAL,
                '12,2',
                [],
                'Value'
            )
            ->addColumn(
                'website_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Website ID'
            )
            ->addIndex(
                $installer->getIdxName('aw_giftcard_product_entity_amounts', ['website_id']),
                ['website_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'aw_giftcard_product_entity_amounts',
                    'entity_id',
                    'catalog_product_entity',
                    'entity_id'
                ),
                'entity_id',
                $installer->getTable('catalog_product_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'aw_giftcard_product_entity_amounts',
                    'website_id',
                    'store_website',
                    'website_id'
                ),
                'website_id',
                $installer->getTable('store_website'),
                'website_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Giftcard Product Amounts Attribute Backend Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_giftcard_product_entity_templates'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_giftcard_product_entity_templates'))
            ->addColumn(
                'value_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ValueId'
            )
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Entity ID'
            )
            ->addColumn(
                'value',
                Table::TYPE_TEXT,
                255,
                [],
                'Value'
            )
            ->addColumn(
                'image',
                Table::TYPE_TEXT,
                255,
                [],
                'Image'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store ID'
            )
            ->addIndex(
                $installer->getIdxName('aw_giftcard_product_entity_templates', ['store_id']),
                ['store_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'aw_giftcard_product_entity_templates',
                    'entity_id',
                    'catalog_product_entity',
                    'entity_id'
                ),
                'entity_id',
                $installer->getTable('catalog_product_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName('aw_giftcard_product_entity_templates', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Giftcard Product Email Templates Attribute Backend Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_giftcard_statistics'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_giftcard_statistics'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Product ID'
            )
            ->addColumn(
                'purchased_qty',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'default' => 0],
                'Purchased Qty'
            )
            ->addColumn(
                'purchased_amount',
                Table::TYPE_DECIMAL,
                '12,2',
                ['default' => '0.00'],
                'Purchased Amount'
            )
            ->addColumn(
                'used_qty',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'default' => 0],
                'Used Qty'
            )
            ->addColumn(
                'used_amount',
                Table::TYPE_DECIMAL,
                '12,2',
                ['default' => '0.00'],
                'Used Amount'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store ID'
            )
            ->addForeignKey(
                $installer->getFkName('aw_giftcard_statistics', 'product_id', 'catalog_product_entity', 'entity_id'),
                'product_id',
                $installer->getTable('catalog_product_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName('aw_giftcard_statistics', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Giftcard Statictics Table');
        $installer->getConnection()->createTable($table);

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

        $installer->endSetup();
    }
}
