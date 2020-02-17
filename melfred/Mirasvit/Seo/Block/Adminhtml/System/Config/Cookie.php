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

class Cookie extends \Magento\Config\Block\System\Config\Form\Field
{
     /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = [])
    {
        $this->_localeDate = $context->getLocaleDate();
        $this->_authorization = $context->getAuthorization();
        $this->mathRandom = $context->getMathRandom();
        $this->_backendSession = $context->getBackendSession();
        $this->formKey = $context->getFormKey();
        $this->nameBuilder = $context->getNameBuilder();
        $this->cookieData = $objectManager->get('Mirasvit\Seo\Model\Cookie\Cookie');
        parent::__construct($context, $data);
    }


    /**
     * Cookie Button Label
     *
     * @var string
     */
    protected $cookieButtonLabel = 'Add cookie';

    /**
     * Set template to itself
     *
     * @return \Mirasvit\Seo\Block\Adminhtml\System\Config\Cookie
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('system/config/cookie.phtml');
        }
        return $this;
    }

    /**
     * Unset some non-related element parameters
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->cookieButtonLabel = $this->cookieData->getButtonLabel();

        $this->addData(
            [
                'button_label' => __($this->cookieButtonLabel),
                'html_id' => $element->getHtmlId(),
                'ajax_url' => $this->_urlBuilder->getUrl('seo/system_config_cookie/change'),
            ]
        );

        return $this->_toHtml();
    }

}
