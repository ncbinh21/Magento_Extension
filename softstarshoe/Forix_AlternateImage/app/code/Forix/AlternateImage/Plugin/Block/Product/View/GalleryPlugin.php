<?php
namespace Forix\AlternateImage\Plugin\Block\Product\View;

use Magento\Catalog\Block\Product\View\Gallery;
use Magento\Framework\Data\Collection;
use Magento\Catalog\Helper\Image as ImageHelper;

/**
 * Class ProductPlugin
 * @package Forix\AlternateImage\Plugin\Block
 */
class GalleryPlugin
{

    /**
     * @var ImageHelper
     */
    protected $_imageHelper;

    /**
     * GalleryPlugin constructor.
     * @param ImageHelper $imageHelper
     */
    public function __construct(
        ImageHelper $imageHelper
    ) {
        $this->_imageHelper = $imageHelper;
    }

    /**
     * Retrieve collection of gallery images
     *
     * @param Gallery $subject
     * @param Collection $result
     * @return Collection
     */
    /*public function afterGetGalleryImages(Gallery $subject, Collection $result)
    {
        if ($result instanceof Collection) {
            foreach ($result as $image) {
                $url = $image->getData('url');
                if (substr($url, -4) == '.gif') {
                    $image->setData(
                        'medium_image_url',
                        $url
                    );
                    $image->setData(
                        'large_image_url',
                        $url
                    );
                }
            }
        }
        return $result;
    }*/

    /**
     * Retrieve product images in JSON format
     *
     * @return string
     */
    public function aroundGetGalleryImagesJson(Gallery $subject, \Closure $proceed)
    {
        if (count($subject->getGalleryImages()) > 0) {
            return $proceed();
        }
        $product = $subject->getProduct();
        $imagesItems = [];
        $imageUrl = $this->_imageHelper->init($product, 'product_page_image_medium')
            ->constrainOnly(true)->keepAspectRatio(true)->keepFrame(false)
            ->setImageFile('')
            ->getUrl();
        $imagesItems[] = [
            'thumb' => $this->_imageHelper->getDefaultPlaceholderUrl('thumbnail'),
            'img' => $imageUrl,
            'full' => $imageUrl,
            'caption' => '',
            'position' => '0',
            'isMain' => true,
        ];

        return json_encode($imagesItems);
    }
}
