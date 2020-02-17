<?php

namespace Forix\Checkout\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\LayoutInterface;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var LayoutInterface
     */
    protected $_layout;

    public function __construct(
        LayoutInterface $layout,
        \Forix\Checkout\Helper\Data $helper
    ) {
        $this->_layout = $layout;
        $this->helper = $helper;
    }

    public function getConfig()
    {
        $config = $this->helper->getConfigPostCode();
        $config['paymentTab'] = $this->helper->getConfigTemplatePayment();
        return $config;
    }
}