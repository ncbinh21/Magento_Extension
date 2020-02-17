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



namespace Mirasvit\Seo\Plugin\Image;

use Mirasvit\Seo\Api\Config\ImageConfigServiceInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\Filter\FilterManager;

class ImageProductRegisterPlugin
{
    /**
     * @param ImageConfigServiceInterface $imageConfig
     * @param Registry $registry
     */
    public function __construct(
        ImageConfigServiceInterface $imageConfig,
        Registry $registry
    ) {
        $this->imageConfig = $imageConfig;
        $this->registry = $registry;
    }

    /**
     * @param \Magento\Catalog\Helper\Image $subject
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @param array $attributes
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeInit($subject, $product, $imageId, $attributes = [])
    {
        if ($this->imageConfig->isEnableImageFriendlyUrl()
            && is_object($product)
            && $product->getId()) {
                $productData = [
                    'product' => $product,
                    'image_id' => $imageId,
                ];
                $this->registry->unregister(ImageConfigServiceInterface::IMAGE_REG_DATA);
                $this->registry->register(ImageConfigServiceInterface::IMAGE_REG_DATA, $productData);
        }
    }
}