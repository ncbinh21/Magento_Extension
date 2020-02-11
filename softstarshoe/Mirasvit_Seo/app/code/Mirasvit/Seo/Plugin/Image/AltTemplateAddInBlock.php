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



namespace Mirasvit\Seo\Plugin\Image;

use Mirasvit\Seo\Model\Config;
use Mirasvit\Seo\Helper\Parse;
use Magento\Catalog\Model\View\Asset\ImageFactory as AssetImageFactory;
use Magento\Framework\View\ConfigInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Catalog\Model\View\Asset\Image as AssetImage;
use Magento\Catalog\Block\Product\ImageFactory;

/**
 * We use this file only for Magento 2.1.6
 * For compilation compatibility with other Magento versions we use  Object Manager
 */
class AltTemplateAddInBlock
{
    /**
     * @var array
     */
    protected $parseObjects = [];

    /**
     * @param \Mirasvit\Seo\Model\Config $config
     * @param \Mirasvit\Seo\Helper\Parse $parse
     */
    public function __construct(
        Config $config,
        Parse $parse,
        ObjectManagerInterface $objectManager,
        ConfigInterface $presentationConfig,
        AssetImageFactory $viewAssetImageFactory,
        ImageFactory $imageBlockFactory
    ) {
        $this->config = $config;
        $this->seoParse = $parse;
        $this->objectManager = $objectManager;
        $this->presentationConfig = $presentationConfig->getViewConfig();
        $this->viewAssetImageFactory = $viewAssetImageFactory;
        $this->imageBlockFactory = $imageBlockFactory;
    }

    /**
     * Get image size
     *
     * @param AssetImage $imageAsset
     * @return array
     * @throws \Exception
     */
    private function getImageSize(AssetImage $imageAsset)
    {
        $this->sizeCache = $this->objectManager->get(\Magento\Catalog\Model\Product\Image\SizeCache::class);
        $imagePath = $imageAsset->getPath();
        $size = $this->sizeCache->load($imagePath);
        if (!$size) {
            $size = getimagesize($imagePath);
            if (!$size) {
                throw new \Exception('An error occurred while reading file: ' . $imagePath);
            }
            $this->sizeCache->save($size[0], $size[1], $imagePath);
            $size = ['width' => $size[0], 'height' => $size[1]];
        }

        return $size;
    }

    /**
     * @param Magento\Catalog\Block\Product\ImageBlockBuilder $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Product $product
     * @param string $displayArea
     * @return Magento\Catalog\Block\Product\Image
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundBuildBlock($subject, $proceed, $product, $displayArea)
    {
        $imageArguments = $this->presentationConfig->getMediaAttributes(
            'Magento_Catalog',
            'images',
            $displayArea
        );

        $image =  $this->objectManager->get(\Magento\Catalog\Model\Product\Image\ParamsBuilder::class)
            ->build($imageArguments);

        $type = isset($imageArguments['type']) ? $imageArguments['type'] : null;
        $baseFilePath = $product->getData($type);
        $baseFilePath = $baseFilePath === 'no_selection' ? null : $baseFilePath;

        $imageAsset = $this->viewAssetImageFactory->create(
            [
                'miscParams' => $image,
                'filePath' => $baseFilePath,
            ]
        );

        $label = $product->getData($imageArguments['type'] . '_' . 'label');
        if (empty($label)) {
            $label = $product->getName();
        }

        //change image alt and title
        if ($this->config->getIsEnableImageAlt() && $this->config->getImageAltTemplate()) {
            $template = $this->config->getImageAltTemplate();
            $this->parseObjects['product'] = $product;
            $label = $this->seoParse->parse($template, $this->parseObjects);
        }

        $frame = isset($imageArguments['frame']) ? $imageArguments ['frame'] : null;
        if (empty($frame)) {
            $frame = $this->presentationConfig->getVarValue('Magento_Catalog', 'product_image_white_borders');
        }

        $template = $frame
            ? 'Magento_Catalog::product/image.phtml'
            : 'Magento_Catalog::product/image_with_borders.phtml';

        $width = $image['image_width'];
        $height = $image['image_height'];

        try {
            $resizedInfo = $this->getImageSize($imageAsset);
        } catch (\Exception $e) {
            $resizedInfo['width'] = $width;
            $resizedInfo['height'] = $height;
        }

        $data = [
            'data' => [
                'template' => $template,
                'image_url' => $imageAsset->getUrl(),
                'width' => $width,
                'height' => $height,
                'label' => $label,
                'ratio' => ($width && $height) ? $height / $width : 1,
                'resized_image_width' => empty($resizedInfo['width']) ? $width : $resizedInfo['width'],
                'resized_image_height' => empty($resizedInfo['height']) ? $height : $resizedInfo['height'],
            ],
        ];

        return $this->imageBlockFactory->create($data);
    }
}