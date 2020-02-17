<?php

namespace Forix\Payment\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$setup->startSetup();

		if (version_compare($context->getVersion(), '1.0.1', '<')) {
			$setup->getConnection()->addColumn(
				$setup->getTable('sales_order'),
				'sales_order_no',
				[
					'type'=>Table::TYPE_INTEGER,
					'unsigned' => true,
					'nullable' => true,
					'comment'  => 'Sales Order No'
				]
			);
		}
		if (version_compare($context->getVersion(), '1.1.1', '<')) {
            $table_forix_payment_orderschedule = $setup->getConnection()->newTable($setup->getTable('forix_payment_orderschedule'));

            $table_forix_payment_orderschedule->addColumn(
                'orderschedule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
                'Entity ID'
            );

            $table_forix_payment_orderschedule->addColumn(
                'sales_order_no',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => False],
                'sales_order_no'
            );

            $table_forix_payment_orderschedule->addColumn(
                'parent_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => False, 'unsigned' => true],
                'Sales Order Increment Id'
            );

            $table_forix_payment_orderschedule->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['nullable' => False],
                'status'
            );

            $table_forix_payment_orderschedule->addColumn(
                'count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => False],
                'process num count'
            );

            $setup->getConnection()->createTable($table_forix_payment_orderschedule);
		}
        if (version_compare($context->getVersion(), '1.1.2', '<')) {
            $connection = $setup->getConnection();
            $schedule = $setup->getTable('forix_payment_orderschedule');

            $sql = "CREATE UNIQUE INDEX forix_payment_orderschedule_sales_order_no_uindex ON {$schedule}(sales_order_no);";
            $connection->query($sql);
        }
        if (version_compare($context->getVersion(), '1.2.1', '<')) {
            $table_forix_payment_customerqueue = $setup->getConnection()->newTable($setup->getTable('forix_payment_customerqueue'));

            $table_forix_payment_customerqueue->addColumn(
                'customerqueue_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
                'Entity ID'
            );

            $table_forix_payment_customerqueue->addColumn(
                'customer_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => False],
                'customer_email'
            );

            $table_forix_payment_customerqueue->addColumn(
                'customer_no',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => False],
                'customer_no'
            );

            $table_forix_payment_customerqueue->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => False, 'default' => 0],
                'Status of the customer'
            );
            $setup->getConnection()->createTable($table_forix_payment_customerqueue);
        }


        if (version_compare($context->getVersion(), '1.2.2', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'sales_order_no',
                [
                    'type'=>Table::TYPE_TEXT,
                    'size' => 20,
                    'unsigned' => true,
                    'nullable' => true,
                    'comment'  => 'Sage100 Sales Order No'
                ]
            );
            $setup->getConnection()->dropColumn($setup->getTable('sales_order'), 'sales_order_no');
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order'),
                'sales_order_no',
                [
                    'type'=>Table::TYPE_TEXT,
                    'size' => 20,
                    'unsigned' => true,
                    'nullable' => true,
                    'comment'  => 'Sales Order No'
                ]
            );
        }
        if (version_compare($context->getVersion(), '1.2.3', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_grid'),
                'sales_order_no',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 32,
                    'comment' => 'Sage100 Sales Order No'
                ]
            );
        }
        if (version_compare($context->getVersion(), '1.2.4', '<')) {
            $tblCCGuId = $setup->getConnection()->newTable($setup->getTable('forix_payment_ccguid'))->addColumn(
                'ccguid_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true, 'auto_increment' => true],
                'Encrypted Credit Card Numb'
            )->addColumn(
                'cc_num',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Encrypted Credit Card Numb'
            )->addColumn(
                'cc_guid',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Credit Card GUID'
            )->addIndex(
                $setup->getIdxName(
                    'forix_payment_ccguid',
                    ['cc_num', 'cc_guid'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['cc_num', 'cc_guid'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            );
            $setup->getConnection()->createTable($tblCCGuId);
		}

		if (version_compare($context->getVersion(), '1.2.5', '<')) {
			$setup->getConnection()->addColumn(
				$setup->getTable('sales_order'),
				'distributor_name',
				[
					'type'     =>Table::TYPE_TEXT,
					'size'     => 100,
					'unsigned' => true,
					'nullable' => true,
					'comment'  => 'Distributor name'
				]
			);


			$setup->getConnection()->addColumn(
				$setup->getTable('sales_order'),
				'distributor_fulfilled',
				[
					'type'     => Table::TYPE_BOOLEAN,
					'nullable' => false,
					'default' => false,
					'comment' => 'distributor_fulfilled'
				]
			);

			$setup->getConnection()->addColumn(
				$setup->getTable('sales_order_grid'),
				'distributor_name',
				[
					'type'    => Table::TYPE_TEXT,
					'length'  => 100,
					'comment' => 'Distributor name'
				]
			);

			$setup->getConnection()->addColumn(
				$setup->getTable('sales_order_grid'),
				'distributor_fulfilled',
				[
					'type'     => Table::TYPE_BOOLEAN,
					'nullable' => false,
					'default'  => false,
					'comment' => 'distributor_fulfilled'
				]
			);

		}

        if (version_compare($context->getVersion(), '1.2.6', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_grid'),
                'sales_order_no',
                [
                    'type'=>Table::TYPE_TEXT,
                    'size' => 20,
                    'unsigned' => true,
                    'nullable' => true,
                    'comment'  => 'Sales Order No'
                ]
            );

        }

        if (version_compare($context->getVersion(), '1.2.7', '<')) {
            $table_forix_payment_customerqueue = $setup->getConnection()->newTable($setup->getTable('forix_payment_customercontactqueue'));

            $table_forix_payment_customerqueue->addColumn(
                'customercontactqueue_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
                'Entity ID'
            );

            $table_forix_payment_customerqueue->addColumn(
                'customer_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => False],
                'Customer contact email'
            );

            $table_forix_payment_customerqueue->addColumn(
                'contact_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => False],
                'Contact code'
            );

            $table_forix_payment_customerqueue->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => False, 'default' => 0],
                'Status of the customer contact'
            );
            $setup->getConnection()->createTable($table_forix_payment_customerqueue);
        }

        if (version_compare($context->getVersion(), '1.2.8', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('forix_payment_customerqueue'),
                'contact_code',
                [
                    'type'=>Table::TYPE_TEXT,
                    'length'  => 100,
                    'nullable' => true,
                    'comment'  => 'Contact Code'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.2.9', '<')) {
            $table_forix_payment_shiptocode = $setup->getConnection()->newTable($setup->getTable('forix_payment_shiptocode'));

            $table_forix_payment_shiptocode->addColumn(
                'shiptocode_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
                'Entity ID'
            );

            $table_forix_payment_shiptocode->addColumn(
                'company_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Company Id'
            );

            $table_forix_payment_shiptocode->addColumn(
                'customer_address_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Customer Address Id'
            );

            $table_forix_payment_shiptocode->addColumn(
                'ship_to_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Ship To Code'
            );

            $setup->getConnection()->createTable($table_forix_payment_shiptocode);
        }

		try {
            $connection = $setup->getConnection();
            $schedule = $setup->getTable('forix_payment_orderschedule');
            $salesOrder = $setup->getTable('sales_order');

            $sql = <<<SQL
select increment_id, sales_order_no from {$salesOrder} where sales_order_no is not null
and sales_order_no not in(select sales_order_no from {$schedule})
SQL;
            $results = $connection->fetchAll($sql);
            if($results && count($results)) {
                $insertData = [];
                foreach ($results as $row) {
                    $insertData[] = [
                        'sales_order_no' => $row['sales_order_no'],
                        'parent_id' => $row['increment_id'],
                        'status' => 1,
                        'count' => 0,
                    ];
                }
                $connection->insertMultiple($schedule, $insertData);
            }
        }catch (\Exception $e){
            die ($e);
        }
		$setup->endSetup();
	}



}
