<?php

namespace Magenest\SagepayUS\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table as Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0') < 0) {
            $this->createVaultTable($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    private function createVaultTable($setup){
        $table = $setup->getConnection()->newTable($setup->getTable('magenest_sagepayus_vault'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Customer ID'
            )
            ->addColumn(
                'card_id',
                Table::TYPE_TEXT,
                50,
                [],
                'Card ID'
            )
            ->addColumn(
                'masked_number',
                Table::TYPE_TEXT,
                20,
                [],
                'Masked Number'
            )
            ->addColumn(
                'card_type',
                Table::TYPE_TEXT,
                10,
                [],
                'Card Type'
            )
            ->addColumn(
                'bin',
                Table::TYPE_TEXT,
                10,
                [],
                'BIN'
            )
            ->addColumn(
                'last_four_digits',
                Table::TYPE_TEXT,
                10,
                [],
                'Last Four Digits'
            )
            ->addColumn(
                'expiration_date',
                Table::TYPE_TEXT,
                10,
                [],
                'Expiration Date'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['default' => Table::TIMESTAMP_INIT],
                'Created At'
            )
            ->addIndex(
                $setup->getIdxName(
                    'magenest_sagepayus_vault',
                    'customer_id'
                ),
                'customer_id'
            )
            ->setComment('Sagepay US vault table');
        $setup->getConnection()->createTable($table);
    }
}
