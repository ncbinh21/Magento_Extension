<?php


namespace Forix\FanPhoto\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\InstallSchemaInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $table_forix_fanphoto_photo = $setup->getConnection()->newTable($setup->getTable('forix_fanphoto_photo'));

        
        $table_forix_fanphoto_photo->addColumn(
            'photo_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );
        

        
        $table_forix_fanphoto_photo->addColumn(
            'image_url',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'image_url'
        );

	    $table_forix_fanphoto_photo->addColumn(
		    'is_active',
		    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
		    null,
		    array('default' => 1),
		    'Is Active'
	    );
        
        $table_forix_fanphoto_photo->addColumn(
            'category_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'category_name'
        );
        

        
        $table_forix_fanphoto_photo->addColumn(
            'caption',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'caption'
        );
        

        
        $table_forix_fanphoto_photo->addColumn(
            'city',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'city'
        );
        

        
        $table_forix_fanphoto_photo->addColumn(
            'state',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'state'
        );
        

        
        $table_forix_fanphoto_photo->addColumn(
            'country',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
	        255,
            [],
            'country'
        );
        

        
        $table_forix_fanphoto_photo->addColumn(
            'firstname',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'firstname'
        );
        

        
        $table_forix_fanphoto_photo->addColumn(
            'lastname',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'lastname'
        );
        

        
        $table_forix_fanphoto_photo->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'email'
        );
        

        
        $table_forix_fanphoto_photo->addColumn(
            'twitter',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'twitter'
        );
        

        
        $table_forix_fanphoto_photo->addColumn(
            'instagram',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'instagram'
        );

	    $table_forix_fanphoto_photo->addColumn(
		    'created_at',
		    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
		    null,
		    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
		    'Created At'
	    );

        $setup->getConnection()->createTable($table_forix_fanphoto_photo);

        $setup->endSetup();
    }
}
