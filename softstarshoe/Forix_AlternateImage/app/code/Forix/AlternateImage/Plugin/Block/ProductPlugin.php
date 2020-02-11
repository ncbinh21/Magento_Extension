<?php

namespace Forix\AlternateImage\Plugin\Block;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Helper\ImageFactory as HelperFactory;
use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Catalog\Helper\Image as ImageHelper;

/**
 * Class ProductPlugin
 * @package Forix\AlternateImage\Plugin\Block
 */
class ProductPlugin extends \Magento\Catalog\Helper\Image
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
     * @param AbstractProduct $subject
     * @param \Magento\Catalog\Model\Product $product
     * @param $imageId
     * @param $attributes
     * @return array
     */
    public function beforeGetImage(
        AbstractProduct $subject,
        $product,
        $imageId,
        $attributes = []
    )
    {
        $alternateImage = $product->getData('alternate_image');
        $smallImage = $product->getData('small_image');
        $newAttributes = [
            'data-alt-src' => ''
        ];
        if($alternateImage && 'no_selection' != $alternateImage) {
            if ($alternateImage != $smallImage) {
                $this->init($product, 'category_page_grid');
                $alternateImagePath = $product->getMediaConfig()->getMediaPath($alternateImage);
                $this->setImageFile($alternateImage);
                try {
                    $imageUrl = $this->getUrl();
                    $newAttributes = [
                        'data-alt-src' => $alternateImage ? $imageUrl : ''
                    ];
                }catch (\Exception $e){}
            }
        }

        $attributes = array_merge(
            $attributes,
            $newAttributes
        );
        return [$product, $imageId, $attributes];
    }
}
