<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Plugin\Model\Backend;

use Magento\Backend\Model\UrlInterface;
use Magento\Backend\Model\Menu\Item;

/**
 * Class MenuItemPlugin
 *
 * @package Aheadworks\Giftcard\Plugin\Model\Backend
 */
class MenuItemPlugin
{
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @param UrlInterface $url
     */
    public function __construct(
        UrlInterface $url
    ) {
        $this->url = $url;
    }

    /**
     * Update discount amount value
     *
     * @param Item $subject
     * @param \Closure $proceed
     * @return string
     */
    public function aroundGetUrl(Item $subject, \Closure $proceed)
    {
        if ($subject->getAction() == 'catalog/product/') {
            return $this->url->getUrl(
                (string)$subject->getAction(),
                ['_cache_secret_key' => true, 'menu' => true]
            );
        }
        return $proceed();
    }
}
