<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 06/09/2018
 * Time: 11:13
 */

namespace Forix\ProductWizard\Block\Adminhtml\Group\Item\Option\Edit;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class Option extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    protected $_optionCollectionFactory;

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
        \Magento\Framework\Registry $coreRegistry,
        \Forix\ProductWizard\Model\ResourceModel\GroupItemOption\CollectionFactory $optionCollectionFactory,
        array $data = []
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->_optionCollectionFactory = $optionCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('group_item_options');
        $this->setDefaultSort('group_item_option_id');
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
     * @return Grid
     */
    protected function _prepareCollection()
    {
        /**
         * @var $collection \Forix\ProductWizard\Model\ResourceModel\GroupItemOption\Collection
         */
        $collection = $this->_optionCollectionFactory->create();
        if ($this->getGroupItem()) {
            $collection->addGroupItemIdToFilter($this->getGroupItem()->getId());
        }
        $collection->addFieldToFilter('product_sku', ['null' => true]);
        $storeId = (int)$this->getRequest()->getParam('store', 0);

        if ($storeId > 0) {
            $collection->addStoreFilter($storeId);
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {
        if (!$this->getGroupItem()) {
            $this->addColumn(
                'in_wizard_item',
                [
                    'type' => 'checkbox',
                    'name' => 'in_wizard_item',
                    'values' => $this->_getSelectedProducts(),
                    'index' => 'group_item_option_id',
                    'header_css_class' => 'col-select col-massaction',
                    'column_css_class' => 'col-select col-massaction'
                ]
            );
        }

        $this->addColumn(
            'group_item_option_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'group_item_option_id'
            ]
        );
        $this->addColumn('title', ['header' => __('Title'), 'index' => 'title']);
        $this->addColumn('option_value', [
                'header' => __('Option Value'),
                'index' => 'option_value',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'sort_order',
            [
                'header' => __('Sort Order'),
                'index' => 'sort_order'
            ]
        );
        $this->addColumn(
            'actions',
            [
                'header' => __('Action'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => 'forix_productwizard/groupitemoption/edit',
                            'params' => []
                        ],
                        'target' => '_blank',
                        'field' => 'group_item_option_id',
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'is_system' => true,
                'header_css_class' => 'col-actions',
                'column_css_class' => 'col-actions'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('forix_productwizard/groupitemoption/grid', ['_current' => true]);
    }

    /**
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('selected_products');
        if ($products === null) {
            $products = $this->getGroupItem()->getOptionCollection()->getAllIds();
            return array_values($products);
        }
        return $products;
    }
}