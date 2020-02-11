<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.2-CE - Soft Start Shoes
 * Date: 27/02/2018
 * Time: 14:51
 */

namespace Forix\InvoicePrint\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SetupInterface;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**@#%
     * @const
     */
    const PREFIX_TABLE_NAME = 'forix_invoiceprint_';

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    private $quoteSetupFactory;

    private $salesSetupFactory;

    /**
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.0.1', '<')) {

            $this->createInvoiceConditionRuleTable($installer);
            $this->createInvoiceConditionStoreTable($installer);
        }

        if (version_compare($context->getVersion(), '1.1.2') < 0) {

            $quoteSetup = $this->quoteSetupFactory->create(['setup' => $setup]);
            $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);

            $itemsAttributesCodes = [
                'sss_product_comment' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, //included_sku in backend.
            ];

            foreach ($itemsAttributesCodes as $code => $type) {
                $quoteSetup->addAttribute('quote_item', $code, ['type' => $type, 'visible' => false]);
                $salesSetup->addAttribute('order_item', $code, ['type' => $type, 'visible' => false]);
            }

        }
        $installer->endSetup();
    }


    /**
     * @param ModuleDataSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function createInvoiceConditionRuleTable(ModuleDataSetupInterface $installer)
    {
        $tableName = self::PREFIX_TABLE_NAME . 'rule';
        try {
            $installer->getConnection()->dropTable($tableName);
        } catch (\Exception $e) {
        }
        if (!$installer->tableExists($tableName)) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable($tableName)
            )->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true],
                'Rule ID'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Rule Name'
            )->addColumn(
                'type',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Type'
            )->addColumn(
                'description',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Description'
            )->addColumn(
                'status',
                Table::TYPE_SMALLINT,
                null,
                [],
                'Status'
            )->addColumn(
                'action',
                Table::TYPE_SMALLINT,
                null,
                [],
                'Action'
            )->addColumn(
                'created_at',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'Created Date'
            )->addColumn(
                'updated_at',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'Updated Date'
            )->addColumn(
                'priority',
                Table::TYPE_TEXT,
                50,
                ['nullable' => true],
                'Priority'
            )->addColumn(
                'conditions_serialized',
                Table::TYPE_TEXT,
                '2M',
                ['nullable' => true],
                'Conditions Serialized'
            )->setComment(
                'Invoice Item Break Page Condition Table'
            );

            $installer->getConnection()->createTable($table);
        }
    }

    /**
     * @param ModuleDataSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function createInvoiceConditionStoreTable(ModuleDataSetupInterface $installer)
    {
        $tableName = self::PREFIX_TABLE_NAME . 'store';
        $ruleTableName = self::PREFIX_TABLE_NAME . 'rule';
        try {
            $installer->getConnection()->dropTable($tableName);
        } catch (\Exception $e) {
        }
        $table = $installer->getConnection()
            ->newTable($installer->getTable($tableName))
            ->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Rule Id'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )
            ->setComment('Invoice Break Page Rules To Store Relations');

        $installer->getConnection()->createTable($table);
    }
}
