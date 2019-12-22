<?php

namespace Forix\CustomAddress\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;


/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var Config
     */
    protected $eavConfig;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * InstallData constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param Config $eavConfig
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory, Config $eavConfig, AttributeSetFactory $attributeSetFactory)
    {
        $this->eavSetupFactory      = $eavSetupFactory;
        $this->attributeSetFactory  = $attributeSetFactory;
        $this->eavConfig            = $eavConfig;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        //create new attribute address
        $this->addLicenseNumberFieldToAddress($setup);
        $this->updateLicenseAttribute($setup);

        $setup->endSetup();
    }

    /**
     * put your comment there...
     *
     * @param mixed $setup
     */
    protected function addLicenseNumberFieldToAddress($setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute('customer_address', 'license_number', [
            'type' => 'varchar',
            'input' => 'text',
            'label' => 'License Number',
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'system'=> false,
            'group'=> 'General',
            'sort_order' => 71,
            'global' => true,
            'visible_on_front' => true,

        ]);

        $customAttribute = $this->eavConfig->getAttribute('customer_address', 'license_number');

        $customAttribute->setData(
            'used_in_forms',
            ['adminhtml_customer_address','customer_address_edit','customer_register_address']
        );
        $customAttribute->save();

    }

    public function updateLicenseAttribute($setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->updateAttribute('customer_address', 'license_number', 'sort_order', '72');
    }

}