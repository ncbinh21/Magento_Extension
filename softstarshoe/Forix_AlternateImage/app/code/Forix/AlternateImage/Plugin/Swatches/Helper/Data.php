<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.2-CE - Soft Star Shoes
 * Date: 19
 * Time: 11:22
 */
namespace Forix\AlternateImage\Plugin\Swatches\Helper;

use Magento\Catalog\Model\Product as ModelProduct;

class Data  extends \Magento\Catalog\Helper\Image
{

    /**
     * Initialize base image file
     *
     * @return $this
     */
    protected function initBaseFile()
    {
        $model = $this->_getModel();
        if ($this->getImageFile()) {
            $model->setBaseFile($this->getImageFile());
        } else {
            $model->setBaseFile($this->getProduct()->getData($model->getDestinationSubdir()));
        }
        return $this;
    }


    /**
     * Check if scheduled actions is allowed
     *
     * @return bool
     */
    protected function isScheduledActionsAllowed()
    {
        return true;
    }

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
    public function aroundGetProductMediaGallery($object, \Closure $proceed, ModelProduct $product)
    {
        $result = $proceed($product);

        $alternateImage = $product->getData('alternate_image');
        $smallImage = $product->getData('small_image');
        $newAttributes = [
            'altSrc' => ''
        ];
        if($alternateImage && 'no_selection' != $alternateImage) {
            if ($alternateImage != $smallImage) {
                $this->init($product, 'category_page_grid');
                $this->setImageFile($alternateImage);
                try {
                    $imageUrl = $this->getUrl();
                    $newAttributes = [
                        'altSrc' => $alternateImage ? $imageUrl : ''
                    ];
                }catch (\Exception $e){}
            }
        }
        return array_merge_recursive($result, $newAttributes);
    }
}