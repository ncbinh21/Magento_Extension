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



namespace Mirasvit\Seo\Api\Service\Twitter;

interface TwitterCardInterface
{
    // twitter
    const TWITTERCARD_SMALL_IMAGE = 1;
    const TWITTERCARD_LARGE_IMAGE = 2;

    /**
     * @param string $body
     * @param string $fullActionCode
     * @return string
     */
    public function addTwitterCard($body, $fullActionCode);

    /**
     * @return void
     */
    public function setTwitterCardType();

    /**
     * @return void
     */
    public function setSiteTag();

    /**
     * @param string $titleTag
     * @return void
     */
    public function setTitleTag($titleTag = '');

    /**
     * @param string $metaDescription
     * @return void
     */
    public function setDescriptionTag($metaDescription = '');

    /**
     * @param string $fullActionCode
     * @return void
     */
    public function setImageTags($fullActionCode);


}