<?php

namespace Forix\Customer\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;

class UpgradeData implements UpgradeDataInterface {

    /**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $collectionCustomerFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Company\Api\CompanyRepositoryInterface
     */
    protected $companyRepository;

    /**
     * UpgradeData constructor.
     * @param \Magento\Company\Api\CompanyRepositoryInterface $companyRepository
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionCustomerFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(
        \Magento\Company\Api\CompanyRepositoryInterface $companyRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionCustomerFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->companyRepository = $companyRepository;
        $this->customerRepository = $customerRepository;
        $this->collectionCustomerFactory = $collectionCustomerFactory;
        $this->customerFactory = $customerFactory;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {// start 1.1.3 version
        $setup->startSetup();
            if ( version_compare( $context->getVersion(), '1.1.3', '<' ) ) {
                    $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        
                    $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
                    $attributeSetId = $customerEntity->getDefaultAttributeSetId();
        
                    /** @var $attributeSet AttributeSet */
                    $attributeSet = $this->attributeSetFactory->create();
                    $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
        
                    $customerSetup->addAttribute(Customer::ENTITY, 'country_name', [
                            'type' => 'varchar',
                            'label' => 'Country Name',
                            'input' => 'select',
                            'required' => false,
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
        }// end 1.1.3 version

        if ( version_compare( $context->getVersion(), '1.1.7', '<' ) ) { // start 1.1.7 version
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

            $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
            $attributeSetId = $customerEntity->getDefaultAttributeSetId();

            /** @var $attributeSet AttributeSet */
            $attributeSet = $this->attributeSetFactory->create();
            $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

            $customerSetup->addAttribute(Customer::ENTITY, 'is_netterm_active', [
                'type'              => 'int',/* Data type in which formate your value save in database*/
                'label'             => __('Is Netterm Active'),
                'input'             => 'select',
                'source'            => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'visible'           => false,
                'required'          => false,
                'system'            => false,
                'position'          => 100,
            ]);

            $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'is_netterm_active')
                ->addData([
                    'attribute_set_id' => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId
                ]);

            $attribute->save();
        } // end 1.1.7 version
        if ( version_compare( $context->getVersion(), '1.2.2', '<' ) ) {
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

            $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
            $attributeSetId = $customerEntity->getDefaultAttributeSetId();

            /** @var $attributeSet AttributeSet */
            $attributeSet = $this->attributeSetFactory->create();
            $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

            $customerSetup->addAttribute(Customer::ENTITY, 'company_title', [
                'type' => 'varchar',
                'label' => 'Company Name',
                'input' => 'text',
                'required' => true,
                'visible' => true,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'user_defined' => true,
                'position' => 850,
                'system' => 0,
            ]);
            $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'company_title')
                ->addData([
                    'attribute_set_id' => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId,
                    'used_in_forms' => ['adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'],//you can use other forms also
                ]);

            $attribute->save();
        }// end 1.2.2 version

        if ( version_compare( $context->getVersion(), '1.2.3', '<' ) ) {
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

            $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
            $attributeSetId = $customerEntity->getDefaultAttributeSetId();

            /** @var $attributeSet AttributeSet */
            $attributeSet = $this->attributeSetFactory->create();
            $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

            $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'customer_no')
                ->addData([
                    'attribute_set_id' => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId,
                    'used_in_forms' => [],//you can use other forms also
                ]);

            $attribute->save();
        }// end 1.2.3 version

        if ( version_compare( $context->getVersion(), '1.2.5', '<' ) ) {
            $customerList = $this->collectionCustomerFactory->create();
            foreach ($customerList as $customerCurrent) {
                try {
                    $customerRes = $this->customerRepository->getById($customerCurrent->getId());
                    if ($customerRes->getExtensionAttributes() && $customerRes->getExtensionAttributes()->getCompanyAttributes() && $companyId = $customerRes->getExtensionAttributes()->getCompanyAttributes()->getCompanyId()) {
                        $company = $this->companyRepository->get($companyId);
                        if($company) {
                            $customer = $this->customerFactory->create()->load($customerCurrent->getId());
                            $customer->setData('company_title', $company->getCompanyName());
                            $customer->getResource()->saveAttribute($customer, 'company_title');
                        }
                    }
                } catch (\Exception $exception) {
                    $customer = $this->customerFactory->create()->load($customerCurrent->getId());
                    $customer->setData('company_title', 'Default');
                    $customer->getResource()->saveAttribute($customer, 'company_title');
                }
            }

        }// end 1.2.5 version

        if ( version_compare( $context->getVersion(), '1.2.6', '<' ) ) { // start 1.2.6 version
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

            $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
            $attributeSetId = $customerEntity->getDefaultAttributeSetId();

            /** @var $attributeSet AttributeSet */
            $attributeSet = $this->attributeSetFactory->create();
            $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

            $customerSetup->addAttribute(Customer::ENTITY, 'skip_create_sage', [
                'type'              => 'int',/* Data type in which formate your value save in database*/
                'label'             => __('Skip Create Customer Sage'),
                'input'             => 'select',
                'source'            => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'visible'           => false,
                'required'          => false,
                'system'            => false,
                'position'          => 100,
            ]);

            $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'skip_create_sage')
                ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId
            ]);

            $attribute->save();
        } // end 1.2.6 version
        $setup->endSetup();
    }
}