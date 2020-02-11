<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Forix\ProductListTemplate\Helper;

use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Catalog image helper
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Image extends \Magento\Catalog\Helper\Image
{
    /**
     * Initialize Helper to work with Image
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @param array $attributes
     * @return $this
     */
    public function init($product, $imageId, $attributes = [])
    {
        $this->_reset();

        $this->attributes = array_merge(
            $this->getConfigView()->getMediaAttributes('Magento_Catalog', self::MEDIA_TYPE_CONFIG_NODE, $imageId),
            $attributes
        );

        $this->setProduct($product);
        $this->setImageProperties();
        $this->setWatermarkProperties();
        $this->_getModel()->setKeepFrame(true);
        $this->_getModel()->setKeepAspectRatio(true);
        $this->_getModel()->setKeepTransparency(false);
        return $this;
    }

}
