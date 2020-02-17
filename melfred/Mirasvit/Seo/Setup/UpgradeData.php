<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Seo\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

//use Magento\Framework\Setup\InstallDataInterface;
//use Magento\Framework\Setup\ModuleContextInterface;
//use Magento\Framework\Setup\ModuleDataSetupInterface;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Catalog\Model\Product;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * InstallData constructor.
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.8') < 0) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->addAttribute(
                Product::ENTITY,
                'm_seo_canonical',
                [
                    'group' => 'Search Engine Optimization',
                    'type' => 'text',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Canonical URL',
                    'input' => 'text',
                    'class' => '',
                    'source' => '',
                    'global' => ScopedAttributeInterface::SCOPE_STORE,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => '',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'unique' => false,
                    'apply_to' => '',
                    'note'          => 'If empty, canonical will be added automatically.
                        As example can be url "http://example.com/url.html" or  "url.html"',
                ]
            );
        }
    }
}