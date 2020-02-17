<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\Shopby\Block\Navigation\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\Store;

class Tooltip extends \Magento\Framework\View\Element\Template implements WidgetInterface
{
    /**
     * @var \Amasty\ShopbyBase\Api\Data\FilterSettingInterface
     */
    protected $filterSetting;

    /**
     * @var string
     */
    protected $_template = 'layer/widget/tooltip.phtml';

    /**
     * @var \Amasty\Shopby\Helper\Data
     */
    protected $helper;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Amasty\Shopby\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    public function setFilterSetting(\Amasty\ShopbyBase\Api\Data\FilterSettingInterface $filterSetting)
    {
        $this->filterSetting = $filterSetting;
        return $this;
    }

    /**
     * @return \Amasty\ShopbyBase\Api\Data\FilterSettingInterface
     */
    public function getFilterSetting()
    {
        return $this->filterSetting;
    }

    /**
     * @return string
     */
    public function getTooltipUrl()
    {
        return $this->helper->getTooltipUrl();
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * @param $storeId
     * @return null|string
     */
    public function getContent($storeId)
    {
        if ($tooltip = $this->getFilterSetting()->getTooltip()) {
            if (preg_match('^([adObis]:|N;)^', $tooltip)) {
                $tooltip = \Zend_Serializer::unserialize($tooltip);

                if (is_array($tooltip)) {
                    $tooltip = isset($tooltip[$storeId])
                        ? $tooltip[$storeId]
                        : (isset($tooltip[Store::DEFAULT_STORE_ID]) ? $tooltip[Store::DEFAULT_STORE_ID] : null);
                }
            }
        }

        return $tooltip;
    }

    /*
     * @param  mixed $valueToEncode
     * @param  boolean $cycleCheck
     * @param  array $options
     * @return string
     */
    public function jsonEncode($valueToEncode, $cycleCheck = false, $options = array())
    {
        return \Zend_Json::encode($valueToEncode, $cycleCheck, $options);
    }
}
