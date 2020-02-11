<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Setup;

use Aheadworks\Giftcard\Model\Source\Entity\Attribute\GiftcardCustomMessage;
use Aheadworks\Giftcard\Model\Source\Entity\Attribute\GiftcardPool;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Catalog\Model\Product;
use Aheadworks\Giftcard\Model\Product\Type\Giftcard as ProductGiftcard;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as CatalogEavAttribute;
use Magento\Framework\App\TemplateTypesInterface;
use Magento\Email\Model\TemplateFactory as EmailTemplateFactory;
use Aheadworks\Giftcard\Model\Email\Sample as SampleEmailTemplate;
use Aheadworks\Giftcard\Api\Data\ProductAttributeInterface;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Quote\Setup\QuoteSetup;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Sales\Setup\SalesSetup;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class InstallData
 *
 * @package Aheadworks\Giftcard\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var string
     */
    private $entityTypeId = Product::ENTITY;

    /**
     * @var string
     */
    private $giftCardInfoGroupName = 'Gift Card Information';

    /**
     * @var string
     */
    private $giftCardTypeCode = ProductGiftcard::TYPE_CODE;

    /**
     * @var int
     */
    private $giftCardInfoGroupSortOrder = 100;

    /**
     * @var EavSetup
     */
    private $eavSetup;

    /**
     * @var QuoteSetupFactory
     */
    private $quoteSetupFactory;

    /**
     * @var SalesSetupFactory
     */
    private $salesSetupFactory;

    /**
     * @var EmailTemplateFactory
     */
    private $emailTemplateFactory;

    /**
     * @var SampleEmailTemplate
     */
    private $sampleEmailTemplate;

    /**
     * @param EavSetup $eavSetup
     * @param QuoteSetupFactory $setupFactory
     * @param SalesSetupFactory $salesSetupFactory
     * @param EmailTemplateFactory $emailTemplateFactory
     * @param SampleEmailTemplate $sampleEmailTemplate
     */
    public function __construct(
        EavSetup $eavSetup,
        QuoteSetupFactory $setupFactory,
        SalesSetupFactory $salesSetupFactory,
        EmailTemplateFactory $emailTemplateFactory,
        SampleEmailTemplate $sampleEmailTemplate
    ) {
        $this->eavSetup = $eavSetup;
        $this->quoteSetupFactory = $setupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->sampleEmailTemplate = $sampleEmailTemplate;
        $this->emailTemplateFactory = $emailTemplateFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var QuoteSetup $quoteSetup */
        $quoteSetup = $this->quoteSetupFactory->create(['setup' => $setup]);
        /** @var SalesSetup $quoteSetup */
        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);

        $attributes = [
            'aw_giftcard_amount' => ['type' => Table::TYPE_DECIMAL],
            'base_aw_giftcard_amount' => ['type' => Table::TYPE_DECIMAL]
        ];
        foreach ($attributes as $attributeCode => $attributeParams) {
            $quoteSetup->addAttribute('quote', $attributeCode, $attributeParams);
            $quoteSetup->addAttribute('quote_address', $attributeCode, $attributeParams);
            $salesSetup->addAttribute('order', $attributeCode, $attributeParams);
            $salesSetup->addAttribute('invoice', $attributeCode, $attributeParams);
            $salesSetup->addAttribute('creditmemo', $attributeCode, $attributeParams);
        }
        $salesSetup->addAttribute('order', 'base_aw_giftcard_invoiced', ['type' => Table::TYPE_DECIMAL]);
        $salesSetup->addAttribute('order', 'aw_giftcard_invoiced', ['type' => Table::TYPE_DECIMAL]);
        $salesSetup->addAttribute('order', 'base_aw_giftcard_refunded', ['type' => Table::TYPE_DECIMAL]);
        $salesSetup->addAttribute('order', 'aw_giftcard_refunded', ['type' => Table::TYPE_DECIMAL]);

        if ($attributeSetId = $this->eavSetup->getAttributeSet($this->entityTypeId, 'Default', 'attribute_set_id')) {
            $this->eavSetup
                ->addAttributeGroup(
                    $this->entityTypeId,
                    $attributeSetId,
                    $this->giftCardInfoGroupName,
                    $this->giftCardInfoGroupSortOrder
                )->updateAttributeGroup(
                    $this->entityTypeId,
                    $attributeSetId,
                    $this->giftCardInfoGroupName,
                    'tab_group_code',
                    'basic'
                );
        }

        $this->eavSetup
            ->addAttribute(
                $this->entityTypeId,
                ProductAttributeInterface::CODE_AW_GC_TYPE,
                [
                    'type' => 'int',
                    'label' => 'Card Type',
                    'input' => 'select',
                    'required' => true,
                    'frontend' => '',
                    'source' => \Aheadworks\Giftcard\Model\Source\Entity\Attribute\GiftcardType::class,
                    'global' => CatalogEavAttribute::SCOPE_GLOBAL,
                    'user_defined' => false,
                    'searchable' => false,
                    'filterable' => false,
                    'visible_in_advanced_search' => false,
                    'used_in_product_listing' => false,
                    'used_for_sort_by' => false,
                    'apply_to' => $this->giftCardTypeCode,
                    'group' => $this->giftCardInfoGroupName,
                    'sort_order' => 1,
                ]
            )->addAttribute(
                $this->entityTypeId,
                ProductAttributeInterface::CODE_AW_GC_DESCRIPTION,
                [
                    'type' => 'text',
                    'label' => 'Card Description',
                    'input' => 'textarea',
                    'wysiwyg_enabled' => true,
                    'required' => false,
                    'global' => CatalogEavAttribute::SCOPE_STORE,
                    'user_defined' => false,
                    'searchable' => false,
                    'filterable' => false,
                    'visible_in_advanced_search' => false,
                    'used_in_product_listing' => false,
                    'used_for_sort_by' => false,
                    'apply_to' => $this->giftCardTypeCode,
                    'group' => $this->giftCardInfoGroupName,
                    'sort_order' => 2,
                ]
            )->addAttribute(
                $this->entityTypeId,
                ProductAttributeInterface::CODE_AW_GC_EXPIRE,
                [
                    'type' => 'int',
                    'label' => 'Expires After (days)',
                    'input' => 'text',
                    'required' => false,
                    'default' => 0,
                    'global' => CatalogEavAttribute::SCOPE_GLOBAL,
                    'user_defined' => false,
                    'searchable' => false,
                    'filterable' => false,
                    'visible_in_advanced_search' => false,
                    'used_in_product_listing' => false,
                    'used_for_sort_by' => false,
                    'apply_to' => $this->giftCardTypeCode,
                    'group' => $this->giftCardInfoGroupName,
                    'sort_order' => 3,
                ]
            )->addAttribute(
                $this->entityTypeId,
                ProductAttributeInterface::CODE_AW_GC_CUSTOM_MESSAGE_FIELDS,
                [
                    'type' => 'int',
                    'label' => 'Custom Message Fields',
                    'input' => 'select',
                    'required' => false,
                    'source' => GiftcardCustomMessage::class,
                    'default' => GiftcardCustomMessage::SHOW_HEADLINE_AND_MESSAGE,
                    'global' => CatalogEavAttribute::SCOPE_STORE,
                    'user_defined' => false,
                    'searchable' => false,
                    'filterable' => false,
                    'visible_in_advanced_search' => false,
                    'used_in_product_listing' => false,
                    'used_for_sort_by' => false,
                    'apply_to' => $this->giftCardTypeCode,
                    'group' => $this->giftCardInfoGroupName,
                    'sort_order' => 4,
                ]
            )->addAttribute(
                $this->entityTypeId,
                ProductAttributeInterface::CODE_AW_GC_EMAIL_TEMPLATES,
                [
                    'type' => 'static',
                    'label' => 'Email Templates',
                    'input' => 'select',
                    'backend' => \Aheadworks\Giftcard\Model\Product\Entity\Attribute\Backend\Templates::class,
                    'frontend' => '',
                    'global' => CatalogEavAttribute::SCOPE_STORE,
                    'user_defined' => false,
                    'searchable' => false,
                    'filterable' => false,
                    'visible_in_advanced_search' => false,
                    'used_in_product_listing' => false,
                    'used_for_sort_by' => false,
                    'apply_to' => $this->giftCardTypeCode,
                    'group' => $this->giftCardInfoGroupName,
                    'sort_order' => 5,
                ]
            )->addAttribute(
                $this->entityTypeId,
                ProductAttributeInterface::CODE_AW_GC_AMOUNTS,
                [
                    'type' => 'static',
                    'label' => 'Amounts',
                    'input' => 'select',
                    'backend' => \Aheadworks\Giftcard\Model\Product\Entity\Attribute\Backend\Amounts::class,
                    'frontend' => '',
                    'required' => false,
                    'global' => CatalogEavAttribute::SCOPE_GLOBAL,
                    'user_defined' => false,
                    'searchable' => false,
                    'filterable' => false,
                    'visible_in_advanced_search' => false,
                    'used_in_product_listing' => false,
                    'used_for_sort_by' => false,
                    'apply_to' => $this->giftCardTypeCode,
                    'group' => $this->giftCardInfoGroupName,
                    'sort_order' => 6,
                ]
            )->addAttribute(
                $this->entityTypeId,
                ProductAttributeInterface::CODE_AW_GC_ALLOW_OPEN_AMOUNT,
                [
                    'type' => 'int',
                    'label' => 'Allow Open Amount',
                    'input' => 'boolean',
                    'required' => false,
                    'frontend' => '',
                    'global' => CatalogEavAttribute::SCOPE_WEBSITE,
                    'user_defined' => false,
                    'searchable' => false,
                    'filterable' => false,
                    'visible_in_advanced_search' => false,
                    'used_in_product_listing' => false,
                    'used_for_sort_by' => false,
                    'apply_to' => $this->giftCardTypeCode,
                    'group' => $this->giftCardInfoGroupName,
                    'sort_order' => 7,
                ]
            )->addAttribute(
                $this->entityTypeId,
                ProductAttributeInterface::CODE_AW_GC_OPEN_AMOUNT_MIN,
                [
                    'type' => 'decimal',
                    'label' => 'Open Amount Min Value',
                    'input' => 'price',
                    'backend' => \Magento\Catalog\Model\Product\Attribute\Backend\Price::class,
                    'required' => false,
                    'global' => CatalogEavAttribute::SCOPE_GLOBAL,
                    'user_defined' => false,
                    'searchable' => false,
                    'filterable' => false,
                    'visible_in_advanced_search' => false,
                    'used_in_product_listing' => false,
                    'used_for_sort_by' => false,
                    'apply_to' => $this->giftCardTypeCode,
                    'group' => $this->giftCardInfoGroupName,
                    'sort_order' => 8,
                ]
            )->addAttribute(
                $this->entityTypeId,
                ProductAttributeInterface::CODE_AW_GC_OPEN_AMOUNT_MAX,
                [
                    'type' => 'decimal',
                    'label' => 'Open Amount Max Value',
                    'input' => 'price',
                    'backend' => \Magento\Catalog\Model\Product\Attribute\Backend\Price::class,
                    'required' => false,
                    'global' => CatalogEavAttribute::SCOPE_GLOBAL,
                    'user_defined' => false,
                    'searchable' => false,
                    'filterable' => false,
                    'visible_in_advanced_search' => false,
                    'used_in_product_listing' => false,
                    'used_for_sort_by' => false,
                    'apply_to' => $this->giftCardTypeCode,
                    'group' => $this->giftCardInfoGroupName,
                    'sort_order' => 9,
                ]
            )->addAttribute(
                $this->entityTypeId,
                ProductAttributeInterface::CODE_AW_GC_ALLOW_DELIVERY_DATE,
                [
                    'type' => 'int',
                    'label' => 'Allow Delivery Date',
                    'input' => 'boolean',
                    'required' => false,
                    'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                    'default' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::VALUE_YES,
                    'global' => CatalogEavAttribute::SCOPE_GLOBAL,
                    'user_defined' => false,
                    'searchable' => false,
                    'filterable' => false,
                    'visible_in_advanced_search' => false,
                    'used_in_product_listing' => false,
                    'used_for_sort_by' => false,
                    'apply_to' => $this->giftCardTypeCode,
                    'group' => $this->giftCardInfoGroupName,
                    'sort_order' => 10,
                ]
            )->addAttribute(
                $this->entityTypeId,
                ProductAttributeInterface::CODE_AW_GC_DAYS_ORDER_DELIVERY,
                [
                    'type' => 'int',
                    'label' => 'Days Between Order and Delivery Dates',
                    'input' => 'text',
                    'required' => false,
                    'default' => 0,
                    'global' => CatalogEavAttribute::SCOPE_GLOBAL,
                    'user_defined' => false,
                    'searchable' => false,
                    'filterable' => false,
                    'visible_in_advanced_search' => false,
                    'used_in_product_listing' => false,
                    'used_for_sort_by' => false,
                    'apply_to' => $this->giftCardTypeCode,
                    'group' => $this->giftCardInfoGroupName,
                    'sort_order' => 11,
                ]
            )->addAttribute(
                $this->entityTypeId,
                ProductAttributeInterface::CODE_AW_GC_POOL,
                [
                    'type' => 'int',
                    'label' => 'Pool',
                    'input' => 'select',
                    'required' => false,
                    'frontend' => '',
                    'source' => GiftcardPool::class,
                    'global' => CatalogEavAttribute::SCOPE_GLOBAL,
                    'user_defined' => false,
                    'searchable' => false,
                    'filterable' => false,
                    'visible_in_advanced_search' => false,
                    'used_in_product_listing' => false,
                    'used_for_sort_by' => false,
                    'apply_to' => $this->giftCardTypeCode,
                    'group' => $this->giftCardInfoGroupName,
                    'sort_order' => 1,
                    'note' => 'if selected pool is empty a new code will be generated as per pool configuration'
                ]
            );

        $fieldListToUpdate = [
            'weight',
            'tax_class_id'
        ];
        foreach ($fieldListToUpdate as $field) {
            $applyTo = explode(
                ',',
                $this->eavSetup->getAttribute($this->entityTypeId, $field, 'apply_to')
            );
            if (!in_array($this->giftCardTypeCode, $applyTo)) {
                $applyTo[] = $this->giftCardTypeCode;
                $this->eavSetup->updateAttribute(
                    $this->entityTypeId,
                    $field,
                    'apply_to',
                    implode(',', $applyTo)
                );
            }
        }

        foreach ($this->sampleEmailTemplate->get() as $data) {
            try {
                /** @var \Magento\Email\Model\Template $template */
                $template = $this->emailTemplateFactory->create()
                    ->load($data['template_code'], 'template_code');
                if (!$template->getId()) {
                    $template
                        ->setData($data)
                        ->setTemplateType(TemplateTypesInterface::TYPE_HTML)
                        ->save();
                }
            } catch (\Exception $e) {
            }
        }
    }
}
