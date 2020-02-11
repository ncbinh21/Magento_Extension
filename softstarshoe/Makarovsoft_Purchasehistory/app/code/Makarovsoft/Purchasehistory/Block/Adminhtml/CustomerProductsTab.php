<?php
namespace Makarovsoft\Purchasehistory\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Registry;
use Magento\Ui\Component\Layout\Tabs\TabWrapper;

class CustomerProductsTab extends TabWrapper
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @var bool
     */
    protected $isAjaxLoaded = true;

    /**
     * @var  \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var  \Magento\Sales\Model\Order\ItemFactory
     */
    protected $_orderItemFactory;


    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(Context $context,
                                Registry $registry,
                                \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $collectionFactory,
                                \Magento\Sales\Model\Order\ItemFactory $orderItemFactory,
                                array $data = [])
    {
        $this->coreRegistry = $registry;

        $this->_collectionFactory = $collectionFactory;
        $this->_orderItemFactory = $orderItemFactory;

        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    public function canShowTab()
    {
        return $this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    protected function getCnt()
    {
        $ids = $collection = $this->_collectionFactory->getReport('sales_order_grid_data_source')->addFieldToSelect(
            'entity_id'
        )->addFieldToFilter(
            'customer_id',
            $this->getRequest()->getParam('id')
        )->getAllIds();

        $collection = $this->_orderItemFactory->create()->getCollection()
            ->addExpressionFieldToSelect('cnt', 'sum({{qty_ordered}})', 'qty_ordered')
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


        return $collection->fetchItem()->getCnt();
    }

    /**
     * Return Tab label
     *
     * @codeCoverageIgnore
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        $cnt = $this->getCnt();


        return __('Purchased Products (%1)', intval($cnt));
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('makarovsoft_purchasehistory/index/products', ['_current' => true]);
    }
}
