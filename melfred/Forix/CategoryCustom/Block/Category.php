<?php

namespace Forix\CategoryCustom\Block;

/**
 * Class Category
 * @package Forix\CategoryCustom\Block
 */
class Category extends \Magento\Framework\View\Element\Template
{

    protected $_coreRegistry = null;
    protected $_catalogLayer;
    protected $_templateProcesser;
    protected $_categoryHelper;

    /**
     * Category constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Widget\Model\Template\Filter $templateProcessor
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Widget\Model\Template\Filter $templateProcessor,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_templateProcesser = $templateProcessor;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getCurrentCategory()
    {
        if (!$this->hasData('current_category')) {
            $this->setData('current_category', $this->_coreRegistry->registry('current_category'));
        }
        return $this->getData('current_category');
    }

    /**
     * @param $string
     * @return string
     */
    public function filterOutputHtml($string)
    {
        return $this->_templateProcesser->filter($string);
    }
}
