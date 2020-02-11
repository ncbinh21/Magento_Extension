<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Email\Variables\CardImageBaseUrl;

use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Giftcard\Model\Product\Type\Giftcard as ProductGiftcard;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Media\Config as MediaConfig;

/**
 * Class Render
 *
 * @package Aheadworks\Giftcard\Model\Email\Variables\CardImageBaseUrl
 */
class Render
{
    /**
     * @var AssetRepository
     */
    private $assetRepo;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var MediaConfig
     */
    private $mediaConfig;

    /**
     * @param AssetRepository $assetRepo
     * @param ProductRepositoryInterface $productRepository
     * @param MediaConfig $mediaConfig
     */
    public function __construct(
        AssetRepository $assetRepo,
        ProductRepositoryInterface $productRepository,
        MediaConfig $mediaConfig
    ) {
        $this->assetRepo = $assetRepo;
        $this->productRepository = $productRepository;
        $this->mediaConfig = $mediaConfig;
    }

    /**
     * Render variable
     *
     * @param int $storeId
     * @param int $productId
     * @param string $templateId
     * @return string
     */
    public function render($storeId, $productId, $templateId)
    {
        $templateImage = $this->getTemplateImageByProduct($storeId, $productId, $templateId);
        return $templateImage
            ? $templateImage . ' "data-default-image="'
            : $this->assetRepo->getUrl('Aheadworks_Giftcard::images/email/cards') . '/';
    }

    /**
     * Retrieve template image by product
     *
     * @param int $storeId
     * @param int $productId
     * @param string $templateId
     * @return string|null
     */
    private function getTemplateImageByProduct($storeId, $productId, $templateId)
    {
        $templateImage = null;
        try {
            $product = $this->productRepository->getById($productId, false, $storeId);
        } catch (NoSuchEntityException $e) {
            return $templateImage;
        }
        if ($product->getTypeId() == ProductGiftcard::TYPE_CODE) {
            $templateOptions = $product->getTypeInstance()->getTemplateOptions($product);
            foreach ($templateOptions as $templateOption) {
                if ($templateOption['template'] == $templateId) {
                    $templateImage = $templateOption['image'];
                }
            }
        }

        return $templateImage
            ? $this->mediaConfig->getTmpMediaUrl($templateImage)
            : $templateImage;
    }
}
