<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Product;

use Magento\Catalog\Helper\Product\Configuration\ConfigurationInterface;
use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;
use Aheadworks\Giftcard\Model\Product\Option\Render as OptionRender;

/**
 * Class Configuration
 *
 * @package Aheadworks\Giftcard\Model\Product
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var OptionRender
     */
    private $optionRender;

    /**
     * @param OptionRender $optionRender
     */
    public function __construct(
        OptionRender $optionRender
    ) {
        $this->optionRender = $optionRender;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(ItemInterface $item)
    {
        $options = [];
        /** @var \Magento\Quote\Model\Quote\Item\Option $option */
        foreach ($item->getOptionsByCode() as $option) {
            $options[$option->getCode()] = $option->getValue();
        }
        return $this->optionRender->render($options, OptionRender::FRONTEND_SECTION);
    }
}
