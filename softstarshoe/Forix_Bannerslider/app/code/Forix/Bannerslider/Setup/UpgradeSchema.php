<?php

namespace Forix\Bannerslider\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '0.2.5') < 0) {
            $installer->getConnection()->addColumn(
                $installer->getTable('forix_bannerslider_banner'),
                'image_thumb',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Banner image'
                ]
            );

            $imageNames = ['tablet','phone'];
            foreach($imageNames as $key) {
                $installer->getConnection()->addColumn(
                    $installer->getTable('forix_bannerslider_banner'),
                    $key,
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => true,
                        'comment' => "$key image"
                    ]
                );
                $installer->getConnection()->addColumn(
                    $installer->getTable('forix_bannerslider_banner'),
                    $key.'_thumb',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => true,
                        'comment' => "$key thumbnail image"
                    ]
                );
            }
        }
        if (version_compare($context->getVersion(), '0.2.6') < 0) {
            $installer->getConnection()->addColumn(
                $installer->getTable('forix_bannerslider_slider'),
                'cms_ids',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'CMS Page IDs'
                ]
            );
        }
        if (version_compare($context->getVersion(), '0.2.7') < 0) {
            $installer->getConnection()->addColumn(
                $installer->getTable('forix_bannerslider_slider'),
                'news_category_ids',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'News Category IDs'
                ]
            );
        }
        if (version_compare($context->getVersion(), '0.2.8') < 0) {
            $installer->getConnection()->addColumn(
                $installer->getTable('forix_bannerslider_slider'),
                'slider_auto_play',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'nullable' => true,
                    'default' => \Forix\Bannerslider\Model\Status::STATUS_ENABLED,
                    'comment' => 'Slider Auto Play'
                ]
            );
        }
        if (version_compare($context->getVersion(), '0.2.9') < 0) {
            $installer->getConnection()->addColumn(
                $installer->getTable('forix_bannerslider_banner'),
                'button_html',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '2M',
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'Button Html'
                ]
            );
        }
        $installer->endSetup();
    }
}