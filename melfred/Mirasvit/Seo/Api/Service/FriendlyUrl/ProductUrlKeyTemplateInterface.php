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



namespace Mirasvit\Seo\Api\Service\FriendlyUrl;

interface ProductUrlKeyTemplateInterface
{
    /**
     * Rewrite type
     */
    const ENTITY_TYPE = 'product';

    /**
     * @return bool|array
     */
    public function getUrlKeyTemplate();

    /**
     * @param string $urlKey
     * @param int    $productId
     * @param int    $storeId
     *
     * @return bool|array
     */
    public function checkUrlKeyUnique($urlKey, $productId, $storeId);

    /**
     * @param string $urlKey
     * @param object $product
     *
     * @return void
     */
    public function applyUrlKey($urlKey, $product);

    /**
     * @param string $urlKey
     * @param object $product
     *
     * @return void
     */
    public function updateEntityUrlKey($urlKey, $product);

}