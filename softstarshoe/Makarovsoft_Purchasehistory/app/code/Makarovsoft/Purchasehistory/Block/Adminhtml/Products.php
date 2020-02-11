<?php
namespace Makarovsoft\Purchasehistory\Block\Adminhtml;

use Magento\Customer\Controller\RegistryConstants;

/**
 * Adminhtml customer orders grid block
 */
class Products extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Sales reorder
     *
     * @var \Magento\Sales\Helper\Reorder
     */
    protected $_salesReorder = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var  \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var  \Magento\Sales\Model\Order\ItemFactory
     */
    protected $_orderItemFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $collectionFactory
     * @param \Magento\Sales\Helper\Reorder $salesReorder
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $collectionFactory,
        \Magento\Sales\Helper\Reorder $salesReorder,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Sales\Model\Order\ItemFactory $orderItemFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_salesReorder = $salesReorder;
        $this->_collectionFactory = $collectionFactory;
        $this->_orderItemFactory = $orderItemFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('customer_orders_products');
        $this->setDefaultSort('created_at', 'desc');
        $this->setUseAjax(true);

        $this->addExportType($this->getUrl('makarovsoft_purchasehistory/export/products', ['_current' => 1]), __('CSV'));

    }

    protected function _prepareMassaction()
    {
//        $this->setMassactionIdField('entity_id');
//        $this->getMassactionBlock()->setTemplate('Magento_Catalog::product/grid/massaction_extended.phtml');
//        $this->getMassactionBlock()->setFormFieldName('product');
//
//        $this->getMassactionBlock()->addItem(
//            'delete',
//            [
//                'label' => __('Delete'),
//                'url' => $this->getUrl('catalog/*/massDelete'),
//                'confirm' => __('Are you sure?')
//            ]
//        );
//
//        $statuses = $this->_status->getOptionArray();
//
//        array_unshift($statuses, ['label' => '', 'value' => '']);
//        $this->getMassactionBlock()->addItem(
//            'status',
//            [
//                'label' => __('Change Status'),
//                'url' => $this->getUrl('catalog/*/massStatus', ['_current' => true]),
//                'additional' => [
//                    'visibility' => [
//                        'name' => 'status',
//                        'type' => 'select',
//                        'class' => 'required-entry',
//                        'label' => __('Status'),
//                        'values' => $statuses
//                    ]
//                ]
//            ]
//        );
//
//            $this->getMassactionBlock()->addItem(
//                'attributes',
//                [
//                    'label' => __('Update Attributes'),
//                    'url' => $this->getUrl('catalog/product_action_attribute/edit', ['_current' => true])
//                ]
//            );
//        $this->_eventManager->dispatch('adminhtml_catalog_product_grid_prepare_massaction', ['block' => $this]);
        return $this;
    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * Apply various selection filters to prepare the sales order grid collection.
     *
     * @return $this
     */
    protected function _prepareCollection()
    {

        $ids = $collection = $this->_collectionFactory->getReport('sales_order_grid_data_source')->addFieldToSelect(
            'entity_id'
        )->addFieldToFilter(
            'customer_id',
            $this->getRequest()->getParam('id')
        )->getAllIds();

        //$ids = [2,3,4,5];
        $collection = $this->_orderItemFactory->create()->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('order_id', ['in' => $ids])
            ->addFieldToFilter('product_type', ['neq' => 'configurable']);

        $tableAlias = 'sales_order_collection';
        $conditions = array(
            "{$tableAlias}.entity_id = main_table.order_id",
        );

        $collection->getSelect()->join(
            array($tableAlias => $collection->getTable('sales_order_grid')),
            implode(' AND ', $conditions),
            array("{$tableAlias}.store_id", "{$tableAlias}.entity_id", "{$tableAlias}.increment_id", "{$tableAlias}.status", "{$tableAlias}.grand_total", "{$tableAlias}.order_currency_code")
        );

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', ['header' => __('Order ID'), 'width' => '100', 'index' => 'increment_id']);
        $this->addColumn(
            'created_at',
            ['header' => __('Purchased'), 'index' => 'created_at', 'type' => 'datetime']
        );



        $this->addColumn('name', ['header' => __('Name'), 'index' => 'name']);
        $this->addColumn('sku', ['header' => __('SKU'), 'index' => 'sku']);
        $this->addColumn('qty_ordered', ['header' => __('Qty'), 'index' => 'qty_ordered']);

        $this->addColumn(
            'grand_total',
            [
                'header' => __('Order Total'),
                'index' => 'grand_total',
                'type' => 'currency',
                'currency' => 'order_currency_code'
            ]
        );

        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn(
                'store_id',
                ['header' => __('Purchase Point'), 'index' => 'store_id', 'type' => 'store', 'store_view' => true]
            );
        }

        return parent::_prepareColumns();
    }

    /**
     * Retrieve the Url for a specified sales order row.
     *
     * @param \Magento\Sales\Model\Order|\Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('sales/order/view', ['order_id' => $row->getOrderId()]);
    }

    /**
     * {@inheritdoc}
     */
    public function getGridUrl()
    {
        return $this->getUrl('customer/*/orders', ['_current' => true]);
    }
}
