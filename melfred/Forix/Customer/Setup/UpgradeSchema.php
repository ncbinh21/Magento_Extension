<?php
/**
 * Copyright (c) 2016.
 * Created by Hidro Le.
 * User: Hidro
 */

namespace Forix\Customer\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var AttributeSetFactory
     */
    protected $attributeSetFactory;

    /**
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    protected $_customerSetupFactory;

    /**
     * UpgradeSchema constructor.
     * @param AttributeSetFactory $attributeSetFactory
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        AttributeSetFactory $attributeSetFactory,
        \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
    ) {
        $this->attributeSetFactory = $attributeSetFactory;
        $this->_customerSetupFactory = $customerSetupFactory;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {

        /** @var \Magento\Customer\Setup\CustomerSetup $customerSetup */
        $customerSetup = $this->_customerSetupFactory->create();

        $installer = $setup;

        $installer->startSetup();
        
        if (version_compare($context->getVersion(), '1.1.0') < 0) {
            $attributesInfo = [
                'mb_attention' => [
                    'type' => 'static',
                    'label' => 'Attention',
                    'input' => 'text',
                    'required' => false,
                    'sort_order' => 55,
                    'user_defined' => 1,
                    'validate_rules' => '{"max_text_length":255,"min_text_length":1}',
                    'position' => 55,
                ]
            ];

            $usedInForms = [
                'adminhtml_customer_address',
                'customer_address_edit',
                'customer_register_address',
            ];
            $data = [];
            $websiteId = [];
            foreach ($attributesInfo as $attributeCode => $attributeParams) {
                $customerSetup->removeAttribute(AddressMetadataInterface::ENTITY_TYPE_ADDRESS, $attributeCode);
                $customerSetup->addAttribute(AddressMetadataInterface::ENTITY_TYPE_ADDRESS, $attributeCode, $attributeParams);
                $attributeId = $customerSetup->getAttributeId(AddressMetadataInterface::ENTITY_TYPE_ADDRESS, $attributeCode);
                foreach ($usedInForms as $formCode) {
                    $data[] = ['form_code' => $formCode, 'attribute_id' => $attributeId];
                }
                $websiteId[] = ['attribute_id' => $attributeId, 'website_id' => 1];
            }

            if ($data) {
                $setup->getConnection()
                    ->insertMultiple($setup->getTable('customer_form_attribute'), $data);
                
                $setup->getConnection()
                    ->insertMultiple($setup->getTable('customer_eav_attribute_website'), $websiteId);
            }
        } //end setup 1.1.0

        if (version_compare($context->getVersion(), '1.1.4','<')) {
            $customerSetup->updateAttribute(
                \Magento\Customer\Model\Customer::ENTITY,
                'country_name',
                'backend_type',
                'static'
            );
            $customerSetup->updateAttribute(
                \Magento\Customer\Model\Customer::ENTITY,
                'country_name',
                'is_user_defined',
                false
            );
            $customerSetup->updateAttribute(
                \Magento\Customer\Model\Customer::ENTITY,
                'country_name',
                'is_required',
                true
            );
        } //end setup 1.1.4

        if (version_compare($context->getVersion(), '1.1.5','<')) {
            $customerSetup->updateAttribute(
                \Magento\Customer\Model\Customer::ENTITY,
                'country_name',
                'is_user_defined',
                true
            );
        } //end setup 1.1.5

        if (version_compare($context->getVersion(), '1.1.6','<')) {

            $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
            $attributeSetId = $customerEntity->getDefaultAttributeSetId();

            /** @var $attributeSet AttributeSet */
            $attributeSet = $this->attributeSetFactory->create();
            $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

            $customerSetup->removeAttribute(Customer::ENTITY, 'country_name');
            $customerSetup->addAttribute(Customer::ENTITY, 'country_name', [
                'type' => 'varchar',
                'label' => 'Country Name',
                'input' => 'select',
                'required' => true,
                'visible' => true,
                'source' => \Magento\Customer\Model\ResourceModel\Address\Attribute\Source\Country::class,
                'user_defined' => true,
                'position' => 800,
                'system' => 0,
            ]);

            $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'country_name')
                ->addData([
                    'attribute_set_id' => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId,
                    'used_in_forms' => ['adminhtml_customer', 'customer_account_create', 'customer_account_edit'],//you can use other forms also
                ]);

            $attribute->save();
        } //end setup 1.1.6

        if (version_compare($context->getVersion(), '1.1.8','<')) {
            $installer->getConnection()->addColumn(
                $installer->getTable('company'),
                'is_netterm_active_company',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'nullable' => true,
                    'comment' => 'Is Netterm Active'
                ]
            );
        } //end setup 1.1.8
        if (version_compare($context->getVersion(), '1.1.9','<')) {

            $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
            $attributeSetId = $customerEntity->getDefaultAttributeSetId();

            /** @var $attributeSet AttributeSet */
            $attributeSet = $this->attributeSetFactory->create();
            $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

            $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'country_name')
                ->addData([
                    'attribute_set_id' => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId,
                    'used_in_forms' => ['adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'],//you can use other forms also
                ]);

            $attribute->save();
        } //end setup 1.1.9

        if (version_compare($context->getVersion(), '1.2.0','<')) {
            $customerSetup->updateAttribute('customer_address', 'company', 'is_required', '1');
        } //end setup 1.2.0

        if (version_compare($context->getVersion(), '1.2.1','<')) {
            $customerSetup->updateAttribute('customer', 'customer_no', 'is_used_in_grid', '1');
            $customerSetup->updateAttribute('customer', 'customer_no', 'is_filterable_in_grid', '1');
            $customerSetup->updateAttribute('customer', 'customer_no', 'is_visible_in_grid', '1');
        } //end setup 1.2.0

        $installer->endSetup();
    }
}





