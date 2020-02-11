<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2 - Soft Star Shoes
 * Date: 1/31/18
 * Time: 11:54 PM
 */

namespace Forix\QuoteLetter\Block\Adminhtml\QuoteLetter\Tab;

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
    protected $_coreRegistry = NULL;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

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
        array $data = []
    )
    {
        $this->_productFactory = $productFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('quoteletter_quoteletter_products');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    /**
     * @return \Forix\QuoteLetter\Model\QuoteLetter
     */
    public function getQuoteLetter()
    {
        return $this->_coreRegistry->registry('forix_quoteletter_quoteletter');
    }

    /**
     * {@inheritdoc}
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in category flag
        if ($column->getId() == 'in_quoteletter') {
            $productSKUs = $this->_getSelectedProducts();
            if (empty($productSKUs)) {
                $productSKUs = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('sku', ['in' => $productSKUs]);
            } elseif (!empty($productSKUs)) {
                $this->getCollection()->addFieldToFilter('sku', ['nin' => $productSKUs]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        if ($this->getQuoteLetter()) {
            $this->setDefaultFilter(['in_quoteletter' => 1]);
        }
        /**
         * @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection
         */
        $collection = $this->_productFactory->create()->getCollection();
        $collection->addAttributeToSelect(
            'name'
        )->addAttributeToSelect(
            'sku'
        )->addAttributeToSelect(
            'price'
        );

        $storeId = (int)$this->getRequest()->getParam('store', 0);
        if ($storeId > 0) {
            $collection->addStoreFilter($storeId);
        }
        $this->setCollection($collection);

        if ($this->getQuoteLetter()) {
            $productSKUs = $this->_getSelectedProducts();
            if (empty($productSKUs)) {
                $productSKUs = '';
            }
            $this->getCollection()->addFieldToFilter('sku', ['in' => $productSKUs]);
        }
        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_quoteletter',
            [
                'type' => 'checkbox',
                'values' => $this->_getSelectedProducts(),
                'index' => 'sku',
                'use_index' => true,
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction'
            ]
        );
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn('name', ['header' => __('Name'), 'index' => 'name']);
        $this->addColumn('sku',
            [
                'header' => __('SKU'),
                'index' => 'sku',
                'sortable' => true,
            ]);
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
        return $this->getUrl('forix_quoteletter/*/grid', ['_current' => true]);
    }

    /**
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('selected_products');
        if ($products === NULL) {
            if($this->getQuoteLetter()) {
                $products = $this->getQuoteLetter()->getProductSKUs();
                return array_values($products);
            }
            return [];
        }
        return $products;
    }
}