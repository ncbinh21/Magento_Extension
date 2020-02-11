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



namespace Mirasvit\Seo\Api\Service\Alternate;

interface UrlInterface
{
    /**
     * Get stores urls.
     *
     * @return array
     */
    public function getStoresCurrentUrl();

    /**
     * @return array
     */
    public function getStores();

    /**
     * @param string $store
     * @return string
     */
    public function getUrlAddition($store);
}