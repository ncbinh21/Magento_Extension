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



namespace Mirasvit\Seo\Api\Config;

interface ImageConfigServiceInterface
{
    const MEDIA_PATH = 'product';
    const DEFAULT_TEMPLATE = '[product_name]';
    const SKU_TEMPLATE = '[product_name]';
    const IMAGE_REG_DATA = 'product_data_for_image_creating';

    const IMAGE_ENABLED_OLD_VERSION = 1;
    const IMAGE_ENABLED = 2;

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function isEnableImageFriendlyUrl($store = null);

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function getImageUrlTemplate($store = null);
}