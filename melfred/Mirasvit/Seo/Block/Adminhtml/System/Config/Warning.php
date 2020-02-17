<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Block\Adminhtml\System\Config;

class Warning extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @param \Magento\Framework\Module\Manager     $moduleManager
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array                                 $data
     */
    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->moduleManager = $moduleManager;
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return parent::_getElementHtml($element);
    }
}
