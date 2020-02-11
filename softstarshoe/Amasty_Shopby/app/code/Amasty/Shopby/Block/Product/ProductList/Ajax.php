<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Block\Product\ProductList;

use Magento\Framework\View\Element\Template;

/**
 * @api
 */
class Ajax extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Amasty\Shopby\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Catalog\Model\Layer
     */
    protected $layer;

    public function __construct(
        Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Amasty\Shopby\Helper\Data $helper,
        array $data = []
    ) {
        $this->layer = $layerResolver->get();
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    public function canShowBlock()
    {
        return $this->helper->isAjaxEnabled();
    }

    public function submitByClick()
    {
        return $this->helper->getSubmitFiltersMode() === \Amasty\Shopby\Model\Source\SubmitMode::BY_BUTTON_CLICK;
    }

    public function scrollUp()
    {
        return $this->_scopeConfig->isSetFlag('amshopby/general/ajax_scroll_up');
    }

    /**
     * Retrieve active filters
     *
     * @return array
     */
    protected function getActiveFilters()
    {
        $filters = $this->layer->getState()->getFilters();
        if (!is_array($filters)) {
            $filters = [];
        }
        return $filters;
    }

    /**
     * Retrieve Clear Filters URL
     *
     * @return string
     */
    public function getClearUrl()
    {
        return $this->helper->getAjaxCleanUrl($this->getActiveFilters());
    }

    public function getCurrentCategoryId()
    {
        return $this->helper->getCurrentCategory()->getId();
    }
}
