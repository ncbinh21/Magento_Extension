<?php
/**
 * *
 *  *
 *  * Copyright © 2017 Forix. All rights reserved.
 *  * See COPYING.txt for license details.
 *
 */
namespace Forix\ProductLabel\Block\Product;

use Magento\Catalog\Block\Product\ImageBuilder as ProductImageBuilder;
use Magento\Catalog\Helper\Image as HelperImage;

/**
 * Class ImageBuilder
 * @package Forix\ProductLabel\Block\Product
 */
class ImageBuilder extends ProductImageBuilder
{


    /**
     * Override Template
     */
    const TEMPLATE_IMAGE = 'Forix_ProductLabel::product/image.phtml';
    const TEMPLATE_IMAGE_WITH_BORDER = 'Forix_ProductLabel::product/image_with_borders.phtml';

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return \Magento\Catalog\Block\Product\ImageFactory
     */
    public function getImageBlock()
    {
        return $this->imageFactory;
    }

    /**
     * @return array
     */
    public function prepareData()
    {
        /** @var \Magento\Catalog\Helper\Image $helper */
        $helper = $this->helperFactory->create()
            ->init($this->product, $this->imageId);
        $url = $helper->getUrl();
        if (substr($url, -4) == '.gif') {
            $smallImage = $this->product->getData('small_image');

            $url = $this->product->getMediaConfig()->getMediaUrl($smallImage);
        }

        $template = $helper->getFrame()
            ? self::TEMPLATE_IMAGE
            : self::TEMPLATE_IMAGE_WITH_BORDER;

        $imagesize = $helper->getResizedImageInfo();

        $data = [
            'data' => [
                'template' => $template,
                'image_url' => $url,
                'width' => $helper->getWidth(),
                'height' => $helper->getHeight(),
                'label' => $helper->getLabel(),
                'ratio' =>  $this->getRatio($helper),
                'custom_attributes' => $this->getCustomAttributes(),
                'resized_image_width' => !empty($imagesize[0]) ? $imagesize[0] : $helper->getWidth(),
                'resized_image_height' => !empty($imagesize[1]) ? $imagesize[1] : $helper->getHeight(),
            ],
        ];

        return $data;
    }
}
