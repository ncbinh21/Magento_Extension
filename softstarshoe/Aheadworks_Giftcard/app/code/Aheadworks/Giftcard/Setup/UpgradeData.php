<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Setup;

use Aheadworks\Giftcard\Model\Source\Entity\Attribute\GiftcardCustomMessage;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Catalog\Model\Product;
use Aheadworks\Giftcard\Api\Data\ProductAttributeInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Quote\Setup\QuoteSetup;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Sales\Setup\SalesSetup;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as CatalogEavAttribute;
use Aheadworks\Giftcard\Model\Product\Type\Giftcard as ProductGiftcard;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Aheadworks\Giftcard\Model\Source\Entity\Attribute\GiftcardPool;

/**
 * Class UpgradeData
 *
 * @package Aheadworks\Giftcard\Setup
 */
class UpgradeData implements UpgradeDataInterface
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
     * @param EavSetup $eavSetup
     * @param QuoteSetupFactory $setupFactory
     * @param SalesSetupFactory $salesSetupFactory
     */
    public function __construct(
        EavSetup $eavSetup,
        QuoteSetupFactory $setupFactory,
        SalesSetupFactory $salesSetupFactory
    ) {
        $this->eavSetup = $eavSetup;
        $this->quoteSetupFactory = $setupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if ($context->getVersion() && version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->updateAttributesForVersion110($setup);
        }
        if ($context->getVersion() && version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->updateAttributesForVersion120($setup);
        }
        $setup->endSetup();
    }

    /**
     * Update attributes for version 1.1.0
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function updateAttributesForVersion110($setup)
    {
        $frontendModelAttributes = [
            ProductAttributeInterface::CODE_AW_GC_TYPE,
            ProductAttributeInterface::CODE_AW_GC_EMAIL_TEMPLATES,
            ProductAttributeInterface::CODE_AW_GC_AMOUNTS,
            ProductAttributeInterface::CODE_AW_GC_ALLOW_OPEN_AMOUNT,
            ProductAttributeInterface::CODE_AW_GC_AMOUNTS
        ];
        foreach ($frontendModelAttributes as $attribute) {
            $this->eavSetup->updateAttribute(
                $this->entityTypeId,
                $attribute,
                'frontend_model',
                null
            );
        }
        $this->eavSetup->updateAttribute(
            $this->entityTypeId,
            ProductAttributeInterface::CODE_AW_GC_AMOUNTS,
            'frontend_input',
            'select'
        );

        // Change attribute_group_code from aw-giftcard-info to gift-card-information
        if ($attributeSetId = $this->eavSetup->getAttributeSet($this->entityTypeId, 'Default', 'attribute_set_id')) {
            $changeGroup = 'aw-giftcard-info';
            // Find attribute group id with code aw-giftcard-info
            $groupId = $this->eavSetup->getAttributeGroup(
                $this->entityTypeId,
                $attributeSetId,
                $changeGroup,
                'attribute_group_id'
            );
            if ($groupId) {
                $this->eavSetup->updateAttributeGroup(
                    $this->entityTypeId,
                    $attributeSetId,
                    $changeGroup,
                    'attribute_group_code',
                    'gift-card-information'
                );
            }
        }
        $this->eavSetup->addAttribute(
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
        );

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

        $connection = $setup->getConnection();
        $this->updateGiftcardOperationTable(
            $connection,
            $setup,
            'aw_giftcard_creditmemo',
            'creditmemo_id',
            'creditmemo_id',
            ['sales_creditmemo' => 'entity_id'],
            '_amount'
        );
        $this->updateGiftcardOperationTable(
            $connection,
            $setup,
            'aw_giftcard_invoice',
            'invoice_id',
            'invoice_id',
            ['sales_invoice' => 'entity_id'],
            '_amount'
        );
        $this->updateGiftcardOperationTable(
            $connection,
            $setup,
            'aw_giftcard_quote',
            'quote_id',
            'quote_id',
            ['quote' => 'entity_id', 'quote_address' => 'quote_id'],
            '_amount'
        );
        $this->updateGiftcardOperationTable(
            $connection,
            $setup,
            'aw_giftcard_quote',
            'quote_id',
            'quote_id',
            ['sales_order' => 'quote_id'],
            '_amount'
        );
        $this->updateGiftcardOperationTable(
            $connection,
            $setup,
            'aw_giftcard_invoice',
            'invoice_id',
            'order_id',
            ['sales_order' => 'entity_id'],
            '_invoiced'
        );
        $this->updateGiftcardOperationTable(
            $connection,
            $setup,
            'aw_giftcard_creditmemo',
            'creditmemo_id',
            'order_id',
            ['sales_order' => 'entity_id'],
            '_refunded'
        );
        $this->copyFromGiftcardQuoteToOrderTable($connection, $setup);
    }

    /**
     * Copy data from Gift Card quote to order table
     *
     * @param AdapterInterface $connection
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function copyFromGiftcardQuoteToOrderTable($connection, $setup)
    {
        $select = $connection->select()
            ->from($setup->getTable('aw_giftcard_quote'));
        $quotes = $connection->fetchAssoc($select);
        foreach ($quotes as $quote) {
            $select = $connection->select()
                ->from($setup->getTable('sales_order'), ['entity_id'])
                ->where('quote_id = ?', $quote['quote_id']);

            if ($orderId = $connection->fetchOne($select)) {
                $data = [
                    'giftcard_id' => $quote['giftcard_id'],
                    'order_id' => $orderId,
                    'base_giftcard_amount' => $quote['base_giftcard_amount'],
                    'giftcard_amount' => $quote['giftcard_amount']
                ];
                $connection->insert(
                    $setup->getTable('aw_giftcard_order'),
                    $data
                );
            }
        }
    }

    /**
     * Update Gift Card operation table
     *
     * @param AdapterInterface $connection
     * @param ModuleDataSetupInterface $setup
     * @param string $table
     * @param string $relationField
     * @param string $field
     * @param [] $updateTables
     * @param string $updateSuffix
     * @return void
     */
    private function updateGiftcardOperationTable(
        $connection,
        $setup,
        $table,
        $field,
        $relationField,
        $updateTables,
        $updateSuffix = ''
    ) {
        $columns = [
            $field,
            'base_giftcard_amount' => 'SUM(base_giftcard_amount)',
            'giftcard_amount' => 'SUM(giftcard_amount)'
        ];
        $group = [$field];
        if ($field != $relationField) {
            $columns[] = $relationField;
            $group = [$relationField];
        }
        $select = $connection->select()
            ->from($setup->getTable($table), [])
            ->columns($columns)
            ->group($group);
        $objects = $connection->fetchAssoc($select);
        foreach ($objects as $object) {
            foreach ($updateTables as $updateTable => $updateRelationField) {
                $connection->update(
                    $setup->getTable($updateTable),
                    [
                        'base_aw_giftcard'. $updateSuffix => $object['base_giftcard_amount'],
                        'aw_giftcard'. $updateSuffix => $object['base_giftcard_amount']
                    ],
                    $updateRelationField . ' = ' . $object[$relationField]
                );
            }
        }
    }

    /**
     * Update attributes for version 1.2.0
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function updateAttributesForVersion120($setup)
    {
        $this->eavSetup->updateAttribute(
            $this->entityTypeId,
            'aw_gc_allow_message',
            'attribute_code',
            ProductAttributeInterface::CODE_AW_GC_CUSTOM_MESSAGE_FIELDS
        );
        $this->eavSetup->updateAttribute(
            $this->entityTypeId,
            ProductAttributeInterface::CODE_AW_GC_CUSTOM_MESSAGE_FIELDS,
            'frontend_label',
            'Custom Message Fields'
        );
        $this->eavSetup->updateAttribute(
            $this->entityTypeId,
            ProductAttributeInterface::CODE_AW_GC_CUSTOM_MESSAGE_FIELDS,
            'source_model',
            GiftcardCustomMessage::class
        );
        $this->eavSetup->addAttribute(
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
    }
}
