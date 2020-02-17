<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 01/08/2018
 * Time: 09:51
 */

namespace Forix\Product\Block\Product\View;

use Magento\Catalog\Api\ProductRepositoryInterface;

class StockMessage extends \Magento\Catalog\Block\Product\View
{
    protected $_mHelperData;
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Forix\Product\Helper\Data $mHelperData,
        array $data = []
    )
    {
        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $data
        );
        $this->_mHelperData = $mHelperData;
    }

    public function getStockMessage()
    {
        $product = $this->getProduct();
        return $this->_mHelperData->getStockMessage($product);
    }
}