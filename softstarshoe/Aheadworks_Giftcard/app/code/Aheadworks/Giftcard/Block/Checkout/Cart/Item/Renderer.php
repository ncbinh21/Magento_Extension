<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Block\Checkout\Cart\Item;

use Aheadworks\Giftcard\Model\Product\Configuration as GiftcardProductConfiguration;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Helper\Product\Configuration as ProductConfig;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Framework\Url\Helper\Data as UrlHelper;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Framework\Pricing\PriceCurrencyInterface as PriceCurrency;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\View\Element\Message\InterpretationStrategyInterface as MessageInterpretationStrategy;
use Magento\Checkout\Block\Cart\Item\Renderer as CartItemRenderer;

/**
 * Gift Card Product items Renderer
 *
 * @package Aheadworks\Giftcard\Block\Checkout\Cart\Item
 */
class Renderer extends CartItemRenderer
{
    /**
     * @var GiftcardProductConfiguration
     */
    private $giftcardProductConfiguration;

    /**
     * @param Context $context
     * @param ProductConfig $productConfig
     * @param CheckoutSession $checkoutSession
     * @param ImageBuilder $imageBuilder
     * @param UrlHelper $urlHelper
     * @param MessageManager $messageManager
     * @param PriceCurrency $priceCurrency
     * @param ModuleManager $moduleManager
     * @param MessageInterpretationStrategy $messageInterpretationStrategy
     * @param GiftcardProductConfiguration $giftcardProductConfiguration
     * @param array $data
     */
    public function __construct(
        Context $context,
        ProductConfig $productConfig,
        CheckoutSession $checkoutSession,
        ImageBuilder $imageBuilder,
        UrlHelper $urlHelper,
        MessageManager $messageManager,
        PriceCurrency $priceCurrency,
        ModuleManager $moduleManager,
        MessageInterpretationStrategy $messageInterpretationStrategy,
        GiftcardProductConfiguration $giftcardProductConfiguration,
        $data = []
    ) {
        parent::__construct(
            $context,
            $productConfig,
            $checkoutSession,
            $imageBuilder,
            $urlHelper,
            $messageManager,
            $priceCurrency,
            $moduleManager,
            $messageInterpretationStrategy,
            $data
        );
        $this->giftcardProductConfiguration = $giftcardProductConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionList()
    {
        return $this->giftcardProductConfiguration->getOptions($this->getItem());
    }
}
