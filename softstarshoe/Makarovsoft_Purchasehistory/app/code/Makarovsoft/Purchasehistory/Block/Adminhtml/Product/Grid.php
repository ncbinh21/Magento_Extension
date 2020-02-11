<?php

/**
 * Adminhtml orders grid
 *
 * @method int getProductId() getProductId()
 * @method \Magento\Review\Block\Adminhtml\Grid setProductId() setProductId(int $productId)
 * @method int getCustomerId() getCustomerId()
 * @method \Magento\Review\Block\Adminhtml\Grid setCustomerId() setCustomerId(int $customerId)
 * @method \Magento\Review\Block\Adminhtml\Grid setMassactionIdFieldOnlyIndexValue() setMassactionIdFieldOnlyIndexValue(bool $onlyIndex)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Makarovsoft\Purchasehistory\Block\Adminhtml\Product;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Review action pager
     *
     * @var \Magento\Review\Helper\Action\Pager
     */
    protected $_reviewActionPager = null;

    /**
     * Review data
     *
     * @var \Magento\Review\Helper\Data
     */
    protected $_reviewData = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Review collection model factory
     *
     * @var \Magento\Review\Model\ResourceModel\Review\Product\CollectionFactory
     */
    protected $_productsFactory;

    /**
     * Review model factory
     *
     * @var \Magento\Review\Model\ReviewFactory
     */
    protected $_reviewFactory;

    /**
     * @var  \Magento\Sales\Model\Order\ItemFactory
     */
    protected $_orderItemFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param \Magento\Review\Model\ResourceModel\Review\Product\CollectionFactory $productsFactory
     * @param \Magento\Review\Helper\Data $reviewData
     * @param \Magento\Review\Helper\Action\Pager $reviewActionPager
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Review\Model\ResourceModel\Review\Product\CollectionFactory $productsFactory,
        \Magento\Review\Helper\Data $reviewData,
        \Magento\Review\Helper\Action\Pager $reviewActionPager,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Sales\Model\Order\ItemFactory $orderItemFactory,
        array $data = []
    ) {
        $this->_productsFactory = $productsFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_reviewData = $reviewData;
        $this->_reviewActionPager = $reviewActionPager;
        $this->_reviewFactory = $reviewFactory;
        $this->_orderItemFactory = $orderItemFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Initialize grid
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('ordersGrid');
        $this->setDefaultSort('created_at');

        $this->addExportType($this->getUrl('makarovsoft_purchasehistory/export/orders', ['_current' => 1]), __('CSV'));
    }

    /**
     * Save search results
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _afterLoadCollection()
    {
        /** @var $actionPager \Magento\Review\Helper\Action\Pager */
        $actionPager = $this->_reviewActionPager;
        $actionPager->setStorageId('orders');
        $actionPager->setItems($this->getCollection()->getAllIds());

        return parent::_afterLoadCollection();
    }

    public function getProductId()
    {
        return $this->getRequest()->getParam('id');
    }


    /**
     * Prepare collection
     *
     * @return \Magento\Review\Block\Adminhtml\Grid
     */
    protected function _prepareCollection()
    {

        $collection = $this->_orderItemFactory->create()->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('product_id', $this->getProductId());

        $tableAlias = 'sales_order_collection';
        $conditions = array(
            "{$tableAlias}.entity_id = main_table.order_id",
        );

        $collection->getSelect()->join(
            array($tableAlias => $collection->getTable('sales_order_grid')),
            implode(' AND ', $conditions),
            array("{$tableAlias}.store_id",
                "{$tableAlias}.entity_id",
                "{$tableAlias}.increment_id",
                "{$tableAlias}.status",
                "{$tableAlias}.customer_name",
                "{$tableAlias}.customer_email",
                "{$tableAlias}.grand_total",
                "{$tableAlias}.order_currency_code"
            )
        );

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return \Magento\Backend\Block\Widget\Grid
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', ['header' => __('Order ID'), 'width' => '100', 'index' => 'increment_id']);

        $this->addColumn('status',
            [
                'header' => __('Status'),
                'type' => 'text',
                'index' => 'status',
                'escape' => true
            ]
        );

        $this->addColumn(
            'created_at',
            ['header' => __('Purchased'), 'index' => 'created_at', 'type' => 'datetime']
        );


        $this->addColumn('customer_name', ['header' => __('Customer Name'), 'index' => 'customer_name']);
        $this->addColumn('customer_email', ['header' => __('Customer Email'), 'index' => 'customer_email']);


//        $this->addColumn('billing_name', ['header' => __('Bill-to Name'), 'index' => 'billing_name']);
//        $this->addColumn('shipping_name', ['header' => __('Ship-to Name'), 'index' => 'shipping_name']);


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


//        $this->addColumn(
//            'action',
//            [
//                'header' => __('Action'),
//                'type' => 'action',
//                'getter' => 'getReviewId',
//                'actions' => [
//                    [
//                        'caption' => __('Edit'),
//                        'url' => [
//                            'base' => 'review/product/edit',
//                            'params' => [
//                                'productId' => $this->getProductId(),
//                                'customerId' => $this->getCustomerId(),
//                                'ret' => $this->_coreRegistry->registry('usePendingFilter') ? 'pending' : null,
//                            ],
//                        ],
//                        'field' => 'id',
//                    ],
//                ],
//                'filter' => false,
//                'sortable' => false
//            ]
//        );

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

    /**
     * Prepare grid mass actions
     *
     * @return void
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->setMassactionIdFilter('entity_id');
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->getMassactionBlock()->setFormFieldName('orders');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl(
                    '*/*/massDelete',
                    ['ret' => $this->_coreRegistry->registry('usePendingFilter') ? 'pending' : 'index']
                ),
                'confirm' => __('Are you sure?')
            ]
        );
    }

    /**
     * Get row url
     *
     * @param \Magento\Review\Model\Review|\Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'sales/order/view',
            [
                'order_id' => $row->getEntityId()
            ]
        );
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        if ($this->getProductId() || $this->getCustomerId()) {
            return $this->getUrl(
                'review/product' . ($this->_coreRegistry->registry('usePendingFilter') ? 'pending' : ''),
                ['productId' => $this->getProductId(), 'customerId' => $this->getCustomerId()]
            );
        } else {
            return $this->getCurrentUrl();
        }
    }
}
