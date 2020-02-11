<?php

namespace Forix\CustomShipping\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\LayoutInterface;

class ConfigProvider implements ConfigProviderInterface
{
    /** @var LayoutInterface */
    protected $_layout;
    protected $urlHelper;

    public function __construct(
        LayoutInterface $layout,
        \Forix\CustomShipping\Helper\Data $helper,
        \Magento\Framework\Url $urlHelper
    )
    {
        $this->_layout = $layout;
        $this->helper = $helper;
        $this->urlHelper = $urlHelper;

    }

    public function getConfig()
    {
        $zipcodeconfig = $this->helper->getConfigValue('forix_custom_checkout/general/postcode');
        $aZipcode = explode(PHP_EOL, $zipcodeconfig);
        $aZips = array();
        $aZipCitys = array();
        foreach ($aZipcode as $item){
            if(trim($item) == '') continue;
            $key_value = explode(',', trim($item));
            $aZipCitys[$key_value[0]] = array($key_value[1], $key_value[2]);
            $aZips[] = $key_value[0];
        }
        return [
            'zipcode_us' => json_encode($aZips),
            'zipcode_full' => json_encode($aZipCitys),
        ];
    }
}