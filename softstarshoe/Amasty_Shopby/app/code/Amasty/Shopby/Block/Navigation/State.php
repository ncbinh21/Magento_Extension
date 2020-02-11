<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Block\Navigation;

use Amasty\Shopby\Model\Layer\Filter\Item;
use Amasty\Shopby\Helper\Data as ShopbyHelper;

class State extends \Magento\LayeredNavigation\Block\Navigation\State
{
    /**
     * @var string
     */
    protected $_template = 'layer/state.phtml';

    /**
     * @var \Amasty\Shopby\Helper\FilterSetting
     */
    protected $filterSettingHelper;

    protected $managerInterface;

    protected $priceCurrency;

    /**
     * @var ShopbyHelper
     */
    protected $helper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Amasty\Shopby\Helper\FilterSetting $filterSettingHelper,
        ShopbyHelper $helper,
        array $data = []
    ) {
        $this->filterSettingHelper = $filterSettingHelper;
        $this->managerInterface = $context->getStoreManager();
        $this->helper = $helper;
        parent::__construct($context, $layerResolver, $data);
    }

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\FilterInterface $filter
     * @return \Amasty\ShopbyBase\Api\Data\FilterSettingInterface
     */
    public function getFilterSetting(\Magento\Catalog\Model\Layer\Filter\FilterInterface $filter)
    {
        return $this->filterSettingHelper->getSettingByLayerFilter($filter);
    }

    /**
     * @param Item $filter
     * @param bool $showLabels
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSwatchHtml(\Amasty\Shopby\Model\Layer\Filter\Item $filter, $showLabels = false)
    {
        return $this->getLayout()->createBlock(\Amasty\Shopby\Block\Navigation\State\Swatch::class)
            ->setFilter($filter)
            ->showLabels($showLabels)
            ->toHtml();
    }

    /**
     * @return string
     */
    public function collectFilters()
    {
        return $this->helper->getSubmitFiltersMode() ===
        \Amasty\Shopby\Model\Source\SubmitMode::BY_BUTTON_CLICK ? '1' : '0';
    }

    /**
     * @param Item $filter
     * @return string
     */
    public function viewLabel($filter)
    {
        $filterSetting = $this->getFilterSetting($filter->getFilter());

        switch ($filterSetting->getDisplayMode()) {
            case \Amasty\Shopby\Model\Source\DisplayMode::MODE_IMAGES:
                $value =  $this->getSwatchHtml($filter);
                break;
            case \Amasty\Shopby\Model\Source\DisplayMode::MODE_IMAGES_LABELS:
                $value =  $this->getSwatchHtml($filter, true);
                break;
            default:
                $value = $this->viewExtendedLabel($filter);
                break;
        }

        return $value;
    }

    /**
     * @param Item $filter
     * @return string
     */
    protected function viewExtendedLabel($filter)
    {
        if ($filter->getFilter()->getRequestVar() == \Amasty\Shopby\Model\Source\DisplayMode::ATTRUBUTE_PRICE) {
            $value = $filter->getLabel();
        } else {
            $value = $this->stripTags($filter->getLabel());
        }

        return $value;
    }
}
