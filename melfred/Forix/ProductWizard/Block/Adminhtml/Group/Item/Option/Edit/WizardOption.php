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

abstract class WizardOption extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    protected $_optionCollectionFactory;

    protected $_groupItemSource;

    protected $_wizardSource;

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
        \Forix\ProductWizard\Model\Source\Group\Items $groupItemSource,
        \Forix\ProductWizard\Model\Source\Wizard $wizardSource,
        \Forix\ProductWizard\Model\ResourceModel\GroupItemOption\CollectionFactory $optionCollectionFactory,
        array $data = []
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->_wizardSource = $wizardSource;
        $this->_groupItemSource = $groupItemSource;
        $this->_optionCollectionFactory = $optionCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }


    /**
     * @return \Forix\ProductWizard\Model\GroupItemOption|null
     */
    public function getGroupItemOption()
    {
        return $this->_coreRegistry->registry('forix_productwizard_group_item_option');
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
        if($itemOption = $this->getGroupItemOption()){
            $itemId = $itemOption->getItemId();
            $collection->addFieldToFilter('item_id', ['neq' => $itemId]);
        }
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        $collection->addFieldToSelect(['title',
                'item_id',
                'wizard_id',
                'option_value',
                'product_sku',
                'sort_order',
                'modify_id' => new \Zend_Db_Expr("CONCAT(item_id, '_', group_item_option_id)")]
        );

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
        $this->addColumn(
            'modify_id',
            [
                'header' => __('Generated Key'),
                'sortable' => true,
                'index' => ['item_id', 'group_item_option_id'],
                'type' => 'concat',
                'separator' => '_',
                'filter_index' => new \Zend_Db_Expr("CONCAT(item_id, '_', group_item_option_id)"),
            ]
        );

        $this->addColumn('group_item_option_id', ['header' => __('ID'), 'index' => 'group_item_option_id']);

        $this->addColumn('title', ['header' => __('Title'), 'index' => 'title']);

        $this->addColumn('item_id',
            [
                'header' => __('Group Item'),
                'index' => 'item_id',
                'type' => 'options',
                'sortable' => false,
                'component' => 'Magento_Ui/js/grid/columns/select',
                'options' => $this->_groupItemSource->getOptionArray(),
                'option_groups' => $this->_groupItemSource->getAllOptions()
            ]
        );

        $this->addColumn('wizard_id', [
                'header' => __('Wizard Title'),
                'index' => 'wizard_id',
                'type' => 'options',
                'sortable' => false,
                'component' => 'Magento_Ui/js/grid/columns/select',
                'options' => $this->_wizardSource->getOptionArray()
            ]
        );
        $this->addColumn('option_value', [
                'header' => __('Option Value'),
                'index' => 'option_value',
            ]
        );

        $this->addColumn('product_sku', [
                'header' => __('Product Sku'),
                'index' => 'product_sku'
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

}