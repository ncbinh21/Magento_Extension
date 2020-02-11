<?php

namespace Orange35\Colorpickercustom\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;

class Config extends AbstractHelper
{
    const XML_PATH_PRODUCT = 'colorpickercustom_section/';

    protected $_scopeConfig;
    protected $_storeScope;

    /**
     * Config constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context
    )
    {
        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_storeScope = ScopeInterface::SCOPE_STORE;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getConfig($name){
        return $this->_scopeConfig->getValue(self::XML_PATH_PRODUCT . $name, $this->_storeScope);
    }

}
