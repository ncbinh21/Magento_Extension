<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 06/09/2018
 * Time: 11:13
 */

namespace Forix\ProductWizard\Block\Adminhtml\Group\Item;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class Product extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;


    protected $_productType;

    protected $_attributeSetOptions;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Model\Product\Type $productType,
        \Magento\Catalog\Model\Product\AttributeSet\Options $attributeSetOptions,
        array $data = []
    )
    {
        $this->_productFactory = $productFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_productType = $productType;
        $this->_attributeSetOptions = $attributeSetOptions;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('catalog_category_products');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setVarNameFilter('filter');
    }

    /**
     * @return \Forix\ProductWizard\Model\GroupItem|null
     */
    public function getGroupItem()
    {
        return $this->_coreRegistry->registry('forix_productwizard_group_item');
    }

    /**
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in category flag
        if ($column->getId() == 'in_wizard_item') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = '';
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } elseif (!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return Grid
     */
    protected function _prepareCollection()
    {
        /**
         * @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection
         */
        $collection = $this->_productFactory->create()->getCollection()
            ->addAttributeToSelect(
            'name'
        )->addAttributeToSelect(
            'sku'
        )->addAttributeToSelect(
            'price'
        )->addAttributeToSelect(
            'type_id'
        )->addAttributeToSelect(
            'attribute_set_id'
        );
        $storeId = (int)$this->getRequest()->getParam('store', 0);

        if ($storeId > 0) {
            $collection->addStoreFilter($storeId);
        }
        $collection->addAttributeToFilter('type_id', \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {

        $this->addColumn(
            'in_wizard_item',
            [
                'type' => 'checkbox',
                'name' => 'in_wizard_item',
                'values' => $this->_getSelectedProducts(),
                'index' => 'entity_id',
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction'
            ]
        );


        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'entity_id'
            ]
        );
        $this->addColumn('name', ['header' => __('Name'), 'index' => 'name']);
        $this->addColumn('sku', [
                'header' => __('SKU'),
                'index' => 'sku',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'type_id',
            [
                'header' => __('Type'),
                'index' => 'type_id',
                'type' => 'options',
                'component' => 'Magento_Ui/js/grid/columns/select',
                'options' => $this->_productType->getOptionArray(),
            ]
        );

        $attributeSets = $this->_attributeSetOptions->toOptionArray();
        $attributeSetOptions = [];
        foreach ($attributeSets as $attributeSet) {
            $attributeSetOptions[$attributeSet['value']] = $attributeSet['label'];
        }
        $this->addColumn(
            'attribute_set_id',
            [
                'header' => __('Attribute Set'),
                'index' => 'attribute_set_id',
                'type' => 'options',
                'component' => 'Magento_Ui/js/grid/columns/select',
                'options' => $attributeSetOptions,
            ]
        );
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'type' => 'currency',
                'currency_code' => (string)$this->_scopeConfig->getValue(
                    \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                ),
                'index' => 'price'
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('forix_productwizard/*/grid', ['_current' => true]);
    }

    /**
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('selected_products');
        if ($products === null) {
            $products = $this->getGroupItem()->getProductIds();
            return array_combine($products, $products);
        }
        return $products;
    }
}