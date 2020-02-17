<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Seo\Api\Config\ImageConfigServiceInterface;

class ImageFriendlyUrl implements ArrayInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Disabled')],
            ['value' => ImageConfigServiceInterface::IMAGE_ENABLED, 'label' => __('Enabled')],
            ['value' => ImageConfigServiceInterface::IMAGE_ENABLED_OLD_VERSION,
                'label' => __('Enabled old version (not recomended)')
            ],
        ];
    }
}
