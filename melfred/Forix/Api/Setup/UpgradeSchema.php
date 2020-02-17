<?php
/**
 * Copyright (c) 2016.
 * Created by Hidro Le.
 * User: Hidro
 */

namespace Forix\Api\Setup;

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
        
        if (version_compare($context->getVersion(), '1.0.2','<')) {

            $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
            $attributeSetId = $customerEntity->getDefaultAttributeSetId();

            /** @var $attributeSet AttributeSet */
            $attributeSet = $this->attributeSetFactory->create();
            $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

            $customerSetup->removeAttribute(Customer::ENTITY, 'customer_No');
            $customerSetup->addAttribute(Customer::ENTITY, 'customer_no', [
                'type' => 'varchar',
                'label' => 'Customer No',
                'input' => 'text',
                'required' => false,
                'visible'  => true,
                'source' => \Magento\Customer\Model\ResourceModel\Address\Attribute\Source\Country::class,
                'user_defined' => true,
                'position' => 800,
                'system' => 0,
            ]);

            $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'customer_no')
                ->addData([
                    'attribute_set_id' => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId,
                    'used_in_forms' => ['adminhtml_customer', 'customer_account_create', 'customer_account_edit'],//you can use other forms also
                ]);

            $attribute->save();
        }

        // remove source model
        if (version_compare($context->getVersion(), '1.0.3','<')) {
	        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
	        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

	        /** @var $attributeSet AttributeSet */
	        $attributeSet = $this->attributeSetFactory->create();
	        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

	        $customerSetup->removeAttribute(Customer::ENTITY, 'customer_no');
	        $customerSetup->addAttribute(Customer::ENTITY, 'customer_no', [
		        'type' => 'varchar',
		        'label' => 'Customer No',
		        'input'    => 'text',
		        'required' => false,
		        'visible'  => false,
		        'source'   => null,
		        'user_defined' => true,
		        'position' => 800,
		        'is_unique' => 1
	        ]);

	        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'customer_no')
		        ->addData([
			        'attribute_set_id' => $attributeSetId,
			        'attribute_group_id' => $attributeGroupId,
			        'used_in_forms' => ['adminhtml_customer', 'customer_account_create', 'customer_account_edit'],//you can use other forms also
		        ]);

	        $attribute->save();
        }



        $installer->endSetup();
    }
}





