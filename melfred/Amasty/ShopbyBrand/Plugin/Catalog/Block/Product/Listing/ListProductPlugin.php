<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBrand
 */


namespace Amasty\ShopbyBrand\Plugin\Catalog\Block\Product\Listing;

use Amasty\ShopbyBrand\Plugin\Catalog\Block\Product\View\BlockHtmlTitlePlugin;
use Magento\Framework\Registry;
use Amasty\ShopbyBrand\Model\Source\Tooltip;

class ListProductPlugin
{
    const DEFAULT_CATEGORY_LOGO_SIZE = 30;
    /**
     * @var BlockHtmlTitlePlugin
     */
    private $blockHtmlTitlePlugin;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $product;

    /**
     * @var \Amasty\ShopbyBrand\Helper\Data
     */
    private $brandHelper;

    public function __construct(
        BlockHtmlTitlePlugin $blockHtmlTitlePlugin,
        Registry $registry,
        \Amasty\ShopbyBrand\Helper\Data $brandHelper
    ) {
        $this->blockHtmlTitlePlugin = $blockHtmlTitlePlugin;
        $this->registry = $registry;
        $this->brandHelper = $brandHelper;
    }

    /**
     * @param $original
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function beforeGetProductDetailsHtml($original, \Magento\Catalog\Model\Product $product)
    {
        $this->setProduct($product);
        return [$product];
    }

    /**
     * Add Brand Label to List Page
     *
     * @param $original
     * @param $html
     * @return string
     */
    public function afterGetProductDetailsHtml($original, $html)
    {
        if ($this->brandHelper->getModuleConfig('general/show_on_listing')) {
            $setting = $this->brandHelper->getModuleConfig('general/tooltip_enabled');
            $data = $this->blockHtmlTitlePlugin->getData();
            $data['show_short_description'] = false;
            $data['width'] = self::DEFAULT_CATEGORY_LOGO_SIZE;
            $data['height'] = self::DEFAULT_CATEGORY_LOGO_SIZE;
            $data['tooltip_enabled'] = in_array(Tooltip::LISTING_PAGE, explode(',', $setting));
            $this->blockHtmlTitlePlugin->setData($data);

            $currentRegistry = $this->registry->registry('current_product') ?: null;
            $this->registry->unregister('current_product');
            $this->registry->register('current_product', $this->getProduct());

            $logoHtml = $this->blockHtmlTitlePlugin->generateLogoHtml();

            $this->registry->unregister('current_product');
            if ($currentRegistry) {
                $this->registry->register('current_product', $currentRegistry);
            }

            $html .= $logoHtml;
        }

        return $html;
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

}
