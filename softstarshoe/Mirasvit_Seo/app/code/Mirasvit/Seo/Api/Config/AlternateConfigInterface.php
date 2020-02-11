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
 * @version   2.0.24
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Api\Config;

interface AlternateConfigInterface
{
    const ALTERNATE_DEFAULT = 1;
    const ALTERNATE_CONFIGURABLE = 2;
    const X_DEFAULT_AUTOMATICALLY = 'AUTOMATICALLY';
    const AMASTY_XLANDING = 'amasty_xlanding_page_view'; //amasty_xlanding page

    /**
     * @param int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function getAlternateHreflang($store);

    /**
     * @param int|\Magento\Store\Model\Store $store
     * @param bool $hreflang
     * @return array|string
     */
    public function getAlternateManualConfig($store, $hreflang);

    /**
     * @param array $storeUrls
     * @return string
     */
    public function getAlternateManualXDefault($storeUrls);

    /**
     * @return bool
     */
    public function isHreflangLocaleCodeAddAutomatical();

    /**
     * @return bool
     */
    public function isHreflangCutCategoryAdditionalData();

    /**
     * @return string|int
     */
    public function getXDefault();

    /**
     * @param int|\Magento\Store\Model\Store $store
     * @return string
     */
    public function getHreflangLocaleCode($store);
}