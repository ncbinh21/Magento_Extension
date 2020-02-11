<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\UrlInterface;

/**
 * Class ConfigProvider
 *
 * @package Aheadworks\Giftcard\Model
 */
class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return [
            'awGiftcard' => [
                'removeUrl' => $this->getRemoveUrl()
            ]
        ];
    }

    /**
     * Get controller URL to remove Gift Card code on cart page
     *
     * @return string
     */
    private function getRemoveUrl()
    {
        return $this->urlBuilder->getUrl('awgiftcard/cart/remove');
    }
}
