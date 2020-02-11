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



namespace Mirasvit\Seo\Api\Service\Snippet;

interface ProductSnippetInterface
{
    // description rich snippets
    const DESCRIPTION_SNIPPETS = 1;
    const META_DESCRIPTION_SNIPPETS = 2;

    //seo condition rich snippets
    const CONDITION_RICH_SNIPPETS_CONFIGURE = 1;
    const CONDITION_RICH_SNIPPETS_NEW_ALL = 2;

    // description rich snippets
    const DESCRIPTION_REGISTER_TAG = 'm__description_tag';


    /**
     * @param Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getBaseProductSnippets($product);

    /**
     * @param Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getManufacturerPartNumber($product);

    /**
     * @param string $product
     * @return $this|string
     */
    public function getImage($product);

    /**
     * @param string $product
     * @return string
     */
    public function getCategoryName($product);

    /**
     * @param string $product
     * @return string
     */
    public function getBrand($product);

    /**
     * @param string $product
     * @return string
     */
    public function getModel($product);

    /**
     * @param string $product
     * @return string
     */
    public function getColor($product);

    /**
     * @param string $product
     * @return string
     */
    public function getWeight($product);

    /**
     * @param string $product
     * @return string
     */
    public function getDimensions($product);

    /**
     * @param string $body
     * @param string $product
     * @return string
     */
    public function getDescription($product);

    /**
     * @param string $product
     * @return string
     */
    public function getAggregateRating($product);

    /**
     * @param string $product
     * @param string $offerSnippets
     * @return string
     */
    public function getOffer($product);

}