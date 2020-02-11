<?php

namespace Orange35\Colorpickercustom\Block\Product\View;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Pricing\Helper\Data as PricingData;
use Magento\Catalog\Helper\Data;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\ArrayUtils;
use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Block\Product\View\Options as CatalogOptions;
use Orange35\Colorpickercustom\Helper\Config;

class Options extends CatalogOptions
{
    protected $_configHelper;

    public function __construct(
        Context $context,
        PricingData $pricingHelper,
        Data $catalogData,
        EncoderInterface $jsonEncoder,
        Option $option,
        Registry $registry,
        ArrayUtils $arrayUtils,
        Config $configHelper,
        array $data = []
    )
    {
        $this->_configHelper = $configHelper;
        parent::__construct($context, $pricingHelper, $catalogData, $jsonEncoder, $option, $registry, $arrayUtils, $data);
    }

    /**
     * Get all options that use colorpicker swatches
     * @return \Magento\Catalog\Model\ResourceModel\Product\Option\Collection
     */
    public function getSwatches()
    {
        return $this->getProduct()->getProductOptionsCollection()->clear()->addFieldToFilter('is_colorpicker', 1)->addValuesToResult()->load();
    }

    /**
     * Get option html block
     * @param Option $option
     * @return string
     */
    public function getOptionHtml(Option $option)
    {
        $type = $this->getGroupOfOption($option->getType());
        if ($option->getIsColorpicker()) {
            switch ($option->getType()) {
                case Option::OPTION_TYPE_MULTIPLE: // break was intentionally omitted
                case Option::OPTION_TYPE_CHECKBOX:
                    $type = 'swatch';
                    $option->setType(Option::OPTION_TYPE_MULTIPLE);
                    break;
                case Option::OPTION_TYPE_DROP_DOWN: // break was intentionally omitted
                case Option::OPTION_TYPE_RADIO:
                    $option->setType(Option::OPTION_TYPE_DROP_DOWN);
                    $type = 'swatch';
                    break;
            }
        }

        $renderer = $this->getChildBlock($type);
        $renderer->setProduct($this->getProduct())->setOption($option);

        return $this->getChildHtml($type, false);
    }

    public function getJsonSliderConfig()
    {
        $config = [];
        $type = $this->_configHelper->getConfig('slider/slider_type');
        $config['slidesShow'] = $type == 1 ? $this->_configHelper->getConfig('slider/slider_swatches') : 1;
        $config['swatchesPerItem'] = $type == 2 ? $this->_configHelper->getConfig('slider/slider_swatches') : 1;
        $config['slidesStep'] = $type == 1 ? $this->_configHelper->getConfig('slider/slider_step') : 1;

        return $this->_jsonEncoder->encode($config);
    }
}
