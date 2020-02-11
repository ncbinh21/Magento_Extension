<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Forix\Custom\Helper;

use Magento\Catalog\Model\Product as ModelProduct;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

/**
 * Class Helper Data
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SwatchData extends \Magento\Swatches\Helper\Data
{
    /**
     * Method getting full media gallery for current Product
     * Array structure: [
     *  ['image'] => 'http://url/pub/media/catalog/product/2/0/blabla.jpg',
     *  ['mediaGallery'] => [
     *      galleryImageId1 => simpleProductImage1.jpg,
     *      galleryImageId2 => simpleProductImage2.jpg,
     *      ...,
     *      ]
     * ]
     * @param ModelProduct $product
     * @return array
     */
    public function getProductMediaGallery(ModelProduct $product)
    {
        if (!in_array($product->getData('image'), [null, self::EMPTY_IMAGE_VALUE], true)) {
            $baseImage = $product->getData('image');
        } else {
            $productMediaAttributes = array_filter($product->getMediaAttributeValues(), function ($value) {
                return $value !== self::EMPTY_IMAGE_VALUE && $value !== null;
            });
            foreach ($productMediaAttributes as $attributeCode => $value) {
                if ($attributeCode !== 'swatch_image') {
                    $baseImage = (string)$value;
                    break;
                }
            }
        }

        if (empty($baseImage)) {
            return [];
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of Object Manager
        $objSession = $objectManager->create('Magento\Framework\Session\SessionManager');

        $resultGallery = $this->getAllSizeImagesCat($product, $baseImage);
        $resultGallery['gallery'] = $this->getGalleryImagesCat($product);
//        if($objSession->getIsInCategoryPage() == 1){
//            $resultGallery = $this->getAllSizeImagesCat($product, $baseImage);
//            $resultGallery['gallery'] = $this->getGalleryImagesCat($product);
//        }else{
//            $resultGallery = $this->getAllSizeImages($product, $baseImage);
//            $resultGallery['gallery'] = $this->getGalleryImages($product);
//        }

        return $resultGallery;
    }

    /**
     * @param ModelProduct $product
     * @param string $imageFile
     * @return array
     */
    private function getAllSizeImagesCat(ModelProduct $product, $imageFile)
    {
        return [
            'large' => $this->imageHelper->init($product, 'category_page_grid')
                ->setImageFile($imageFile)
                ->getUrl(),
            'medium' => $this->imageHelper->init($product, 'category_page_grid')
                ->setImageFile($imageFile)
                ->getUrl(),
            'small' => $this->imageHelper->init($product, 'category_page_grid')
                ->setImageFile($imageFile)
                ->getUrl(),
        ];
    }

    /**
     * @param ModelProduct $product
     * @return array
     */
    private function getGalleryImages(ModelProduct $product)
    {
        //TODO: remove after fix MAGETWO-48040
        $product = $this->productRepository->getById($product->getId());

        $result = [];
        $mediaGallery = $product->getMediaGalleryImages();
        foreach ($mediaGallery as $media) {
            $result[$media->getData('value_id')] = $this->getAllSizeImages(
                $product,
                $media->getData('file')
            );
        }
        return $result;
    }

    /**
     * @param ModelProduct $product
     * @return array
     */
    private function getGalleryImagesCat(ModelProduct $product)
    {
        //TODO: remove after fix MAGETWO-48040
        $product = $this->productRepository->getById($product->getId());

        $result = [];
        $mediaGallery = $product->getMediaGalleryImages();
        foreach ($mediaGallery as $media) {
            $result[$media->getData('value_id')] = $this->getAllSizeImagesCat(
                $product,
                $media->getData('file')
            );
        }
        return $result;
    }

    /**
     * @param ModelProduct $product
     * @param string $imageFile
     * @return array
     */
    private function getAllSizeImages(ModelProduct $product, $imageFile)
    {
        return [
            'large' => $this->imageHelper->init($product, 'product_page_image_large_no_frame')
                ->setImageFile($imageFile)
                ->getUrl(),
            'medium' => $this->imageHelper->init($product, 'product_page_image_medium_no_frame')
                ->setImageFile($imageFile)
                ->getUrl(),
            'small' => $this->imageHelper->init($product, 'product_page_image_small')
                ->setImageFile($imageFile)
                ->getUrl(),
        ];
    }
}
