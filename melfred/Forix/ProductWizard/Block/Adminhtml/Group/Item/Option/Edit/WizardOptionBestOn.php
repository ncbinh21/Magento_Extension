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

/**
 * Class WizardOptionBestOn
 * @method \Forix\ProductWizard\Model\ResourceModel\GroupItemOption\Collection getCollection()
 * @package Forix\ProductWizard\Block\Adminhtml\Group\Item\Option\Edit
 */
class WizardOptionBestOn extends WizardOption
{

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('group_item_option_best_on');
        $this->setDefaultSort('group_item_option_id');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setVarNameFilter('filter');
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('forix_productwizard/groupitemoption/gridbeston', ['_current' => true]);
    }

    /**
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in category flag
        if ($column->getId() == 'option_best_on') {
            $productIds = $this->_getSelectedProducts();
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter(new \Zend_Db_Expr("CONCAT(item_id, '_', group_item_option_id)"), ['in' => $productIds]);
            } elseif (!empty($productIds)) {
                $this->getCollection()->addFieldToFilter(new \Zend_Db_Expr("CONCAT(item_id, '_', group_item_option_id)"), ['nin' => $productIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'option_best_on',
            [
                'type' => 'checkbox',
                'name' => 'option_best_on',
                'values' => $this->_getSelectedProducts(),
                'index' => 'modify_id',
                'use_index' => true,
                'header_css_class' => 'col-select',
                'column_css_class' => 'col-select'
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $bestOn = $this->getRequest()->getPost('best_on');
        if (!$bestOn) {
            $bestOn = $this->getGroupItemOption()->getBestOn();
            return array_combine($bestOn, $bestOn);
        }
        return $bestOn;
    }
}