<?php


namespace Forix\Media\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

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

        $table_forix_media_video = $setup->getConnection()->newTable($setup->getTable('forix_media_video'));

        
        $table_forix_media_video->addColumn(
            'video_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );
        

        
        $table_forix_media_video->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'title'
        );
        

        
        $table_forix_media_video->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'description'
        );
        

        
        $table_forix_media_video->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'product_id'
        );
        

        
        $table_forix_media_video->addColumn(
            'media_url',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'media_url'
        );
        

        $setup->getConnection()->createTable($table_forix_media_video);

        $setup->endSetup();
    }
}
