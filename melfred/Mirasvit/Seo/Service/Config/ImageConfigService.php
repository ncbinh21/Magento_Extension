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



namespace Mirasvit\Seo\Service\Config;

use Magento\Store\Model\ScopeInterface as ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Mirasvit\Seo\Api\Config\ImageConfigServiceInterface;

class ImageConfigService implements ImageConfigServiceInterface
{
    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnableImageFriendlyUrl($store = null)
    {
        return $this->scopeConfig->getValue(
            'seo/image/is_enable_image_friendly_url',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getImageUrlTemplate($store = null)
    {
        $imageUrlTemplate = $this->scopeConfig->getValue(
            'seo/image/image_url_template',
            ScopeInterface::SCOPE_STORE,
            $store
        );

        if (!$imageUrlTemplate) {
            $imageUrlTemplate = self::DEFAULT_TEMPLATE;
        }

        return $imageUrlTemplate;
    }
}
