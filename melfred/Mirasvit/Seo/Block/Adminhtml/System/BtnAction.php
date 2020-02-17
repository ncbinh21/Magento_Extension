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



namespace Mirasvit\Seo\Block\Adminhtml\System;

class BtnAction extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Magento\Backend\Model\Url
     */
    protected $url;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @param \Magento\Backend\Model\Url            $url
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array                                 $data
     */
    public function __construct(
        \Magento\Backend\Model\Url $url,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->url = $url;
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('Mirasvit_Seo::system/btn_action.phtml');
        }

        return $this;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();

        return parent::render($element);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $this->addData([
            'button_label' => __($originalData['button_label']),
            'html_id' => $element->getHtmlId(),
            'ajax_url' => $this->url->getUrl('seo/adminhtml_system_template/'.$originalData['button_action']),
        ]);

        return $this->_toHtml();
    }
}
