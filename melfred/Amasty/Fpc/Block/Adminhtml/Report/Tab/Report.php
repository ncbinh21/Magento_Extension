<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Block\Adminhtml\Report\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\View\Element\Template;

abstract class Report extends Template implements TabInterface
{
    abstract protected function _getGraphData();

    /**
     * Return Tab title
     *
     * @return string
     * @api
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     * @api
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     * @api
     */
    public function isHidden()
    {
        return false;
    }

    protected function _toHtml()
    {
        if (!$this->getGraphData()) {
            return __('Sorry, but there is no data available yet. Please check back later.');
        } else {
            return parent::_toHtml();
        }
    }

    public function getGraphData()
    {
        if (!$this->hasData('graph_data')) {
            $this->setData('graph_data', $this->_getGraphData());
        }

        return $this->getData('graph_data');
    }
}
