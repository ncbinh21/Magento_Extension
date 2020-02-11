<?php
namespace Forix\Custom\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Model\Category;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Customer\Setup\CustomerSetupFactory;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    protected $customerSetupFactory;
    /**
     * UpgradeData constructor.
     * @param EavSetupFactory $eavSetupFactory
     */
    public function  __construct(
        EavSetupFactory $eavSetupFactory,
        CustomerSetupFactory $customerSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->customerSetupFactory = $eavSetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup,ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.1') < 0) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->removeAttribute(Category::ENTITY, 'sss_is_load');
            $eavSetup->addAttributeGroup(
                Category::ENTITY,
                $eavSetup->getDefaultAttributeSetId(Category::ENTITY),
                'Forix',
                99
            );
            $eavSetup->addAttribute(Category::ENTITY, 'sss_is_load',
                [
                        'group' => 'Forix',
                        'type' => 'int',
                        'backend' => '',
                        'frontend' => '',
                        'label' => 'Is load',
                        'input' => 'boolean',
                        'sort_order' => 99,
                        'class' => '',
                        'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'used_in_product_listing' => true,
                        'default' => '1',
                ]
            );
        };//end upgrade 2.0.1

        if (version_compare($context->getVersion(), '2.0.2') < 0) {
            $setup->getConnection()->query("UPDATE `sales_sequence_profile` SET start_value = 600000000, prefix = '' WHERE meta_id = 2");
            $setup->getConnection()->query("UPDATE `sales_sequence_profile` SET start_value = 600000000, prefix = '' WHERE meta_id = 9");
            $setup->getConnection()->query("UPDATE `sales_sequence_profile` SET start_value = 600000000, prefix = '' WHERE meta_id = 23");
            $setup->getConnection()->query("ALTER TABLE sequence_order_1 AUTO_INCREMENT=600000000");
            $setup->getConnection()->query("ALTER TABLE sequence_invoice_1 AUTO_INCREMENT=600000000");
            $setup->getConnection()->query("ALTER TABLE sequence_shipment_1 AUTO_INCREMENT=600000000");
        };//end upgrade 2.0.2
        if (version_compare($context->getVersion(), '2.0.3') < 0) {
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
            $this->upgradePostCode($customerSetup);
        }
    }
    private function upgradePostCode($customerSetup)
    {
        $customerSetup->updateAttribute('customer_address', 'postcode', 'is_required', '1');
    }
}