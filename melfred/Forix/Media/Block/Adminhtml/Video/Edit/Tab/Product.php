<?php

namespace Forix\Media\Block\Adminhtml\Video\Edit\Tab;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class Product extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $logger;
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


    protected $_productCollectionFactory;


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Forix\Media\Model\ResourceModel\Video\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_productFactory = $productFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_productCollectionFactory = $productCollectionFactory;
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
    }

    /**
     * @return array|null
     */
    public function getItem()
    {
        return $this->_coreRegistry->registry('forix_media_video');
    }

    /**
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {

        // Set custom filter for in category flag
        if ($column->getId() == 'in_category') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
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
        if ($this->getItem() && $this->getItem()->getId()) {
            $productId = $this->getItem()->getProductId();
            $this->setDefaultFilter(['in_category' => 1]);
        }
        $collection = $this->_productFactory->create()->getCollection()->addAttributeToSelect(
            'name'
        )->addAttributeToSelect(
            'sku'
        )->addAttributeToSelect(
            'price'
        )->joinField(
            'position',
            'catalog_category_product',
            'position',
            'product_id=entity_id',
            'category_id=' . (int)$this->getRequest()->getParam('id', 0),
            'left'
        );
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        if ($storeId > 0) {
            $collection->addStoreFilter($storeId);
        }


        $this->setCollection($collection);

        if ($this->getItem() && $this->getItem()->getProductId()) {
//            $productIds = $this->_getSelectedProducts();
//            if (empty($productIds)) {
//                $productIds = 0;
//            }
            $this->getCollection()->addFieldToFilter('entity_id', $this->getItem()->getProductId());
        } elseif ($this->getItem() && !$this->getItem()->getProductId()) {
            $this->getCollection()->addFieldToFilter('entity_id', '');
        }

        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {
        $select = null;
        if($this->getItem()) {
            $select = $this->_getSelectedProducts();
        }
        $this->addColumn(
            'in_category',
            [
                'type' => 'checkbox',
                'name' => 'in_category',
                'values' => $select,
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
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn('name', ['header' => __('Name'), 'index' => 'name']);
        $this->addColumn('sku', ['header' => __('SKU'), 'index' => 'sku']);
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
        return $this->getUrl('forix_media/*/grid', ['_current' => true]);
    }

    /**
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('selected_products');
        if($this->getItem()){
            if ($products === null) {
                $vProducts = $this->_productCollectionFactory->create()
                    ->addFieldToSelect('product_id');
                $products = array();
                foreach($vProducts as $pdct){
                    $products[]  = $pdct->getProductId();
                }
            }
        }
        return $products;
    }
}