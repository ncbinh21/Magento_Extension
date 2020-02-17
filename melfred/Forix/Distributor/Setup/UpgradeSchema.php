<?php

namespace Forix\Distributor\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    protected $collectionCompanyDistributor;
    protected $companyRepository;
    protected $resourceConnection;

    public function __construct(
        \Forix\Distributor\Model\ResourceModel\CompanyDistributor\CollectionFactory $collectionCompanyDistributor,
        \Magento\Company\Api\CompanyRepositoryInterface $companyRepository,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->collectionCompanyDistributor = $collectionCompanyDistributor;
        $this->companyRepository = $companyRepository;
        $this->resourceConnection = $resourceConnection;
    }

    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    )
    {
        $setup->startSetup();
        /**
         * Remove distributor zipcode & change Logic distributor zipcode
         */
        if (version_compare($context->getVersion(), '1.2.0') < 0) {
            $tableName = $setup->getTable('company');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();
                $connection->dropColumn($tableName, 'distributor_zipcode');
            }


            $company_distributors = $setup->getConnection()->newTable($setup->getTable('company_distributors'));

            $company_distributors->addColumn(
                'row_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
                'Entity ID'
            );

            $company_distributors->addColumn(
                'company_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Linked company ID'
            );


            $company_distributors->addColumn(
                'distributor_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Linked Distributor(Location) ID'
            );
            $setup->getConnection()->createTable($company_distributors);
        }

        if (version_compare($context->getVersion(), '1.2.1') < 0) {
            $connection = $setup->getConnection();
            if ($setup->tableExists('company_distributors')) {
                $connection->changeColumn(
                    'company_distributors', 'company_id', 'company_id', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 'unsigned' => true, 'nullable' => false, 'primary' => true,]
                );

                $connection->addForeignKey(
                    $setup->getFkName(
                        'company_distributors',
                        'company_id',
                        'company',
                        'entity_id'
                    ),
                    $setup->getTable('company_distributors'),
                    'company_id',
                    $setup->getTable('company'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );
            }
        } //end setup 1.2.1

        if (version_compare($context->getVersion(), '1.2.3') < 0) {
            $allCompanyDistributor = $this->collectionCompanyDistributor->create();
            if($allCompanyDistributor->count() >0) {
                foreach ($allCompanyDistributor as $companyDistributor) {
                    try{
                        $this->companyRepository->get($companyDistributor->getCompanyId());
                    } catch (\Exception $exception) {
                        $delete = 'delete from `company_distributors` where row_id = ' . $companyDistributor->getRowId();
                        $this->resourceConnection->getConnection()->query($delete);
                    }
                }
            }
        }//end setup 1.2.2
        $setup->endSetup();
    }
}