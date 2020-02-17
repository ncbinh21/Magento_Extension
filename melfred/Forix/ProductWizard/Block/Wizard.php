<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 22/06/2018
 * Time: 03:07
 */

namespace Forix\ProductWizard\Block;


use Magento\Framework\View\Element\Template;

class Wizard extends \Magento\Framework\View\Element\Template
{
    protected $_registry;
    protected $_localeFormat;

    public function __construct(
        Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        array $data = []
    )
    {
        $this->_localeFormat = $localeFormat;
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    public function getPriceFormat()
    {
        return $this->_localeFormat->getPriceFormat();
    }

    public function getStoreCode()
    {
        return $this->_storeManager->getStore()->getCode();
    }

    /**
     * @return array
     */
    public function getDefaultData()
    {
        $params = $this->getRequest()->getParams();
        if ($savedKey = $this->getRequest()->getParam('key')) {
            $params = array_merge($params, [
                'defaults' => [],
            ]);
        }
        return $params;
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * @return \Forix\ProductWizard\Model\Wizard
     */
    public function getCurrentWizard()
    {
        return $this->_registry->registry('current_wizard');
    }

    public function _prepareLayout()
    {
        if ($wizard = $this->getCurrentWizard()) {
            $this->pageConfig->getTitle()->set(__($wizard->getTitle()));
        }
        return parent::_prepareLayout();
    }
}