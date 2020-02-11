<?php

namespace Forix\Custom\Block;

use Magento\Framework\View\Element\Template;
use Aheadworks\Giftcard\Api\Data\ProductAttributeInterface;

class Product extends \Magento\Catalog\Block\Product\AbstractProduct
{

    /**
     * @var
     */
    protected $collection;

    /**
     * @var \Forix\Custom\Helper\ResizeImage
     */
    protected $resizeImage;

    /**
     * @var \Forix\Custom\Helper\HelperData
     */
    protected $helperData;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * Product constructor.
     * @param Product\ImageBuilderCustom $imageBuilderCustom
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Forix\Custom\Helper\HelperData $helperData
     * @param array $data
     */
    public function __construct(
        \Forix\Custom\Block\Product\ImageBuilderCustom $imageBuilderCustom,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Forix\Custom\Helper\HelperData $helperData,
        array $data = []
    ) {
        $this->imageBuilderCustom = $imageBuilderCustom;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->productRepository = $productRepository;
        $this->helperData = $helperData;
        parent::__construct($context, $data);
    }

    /**
     * @param $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCollection(){
        if(is_null($this->collection)) {
            $productCollection = $this->categoryCollectionFactory->create();
            $productCollection->addAttributeToSelect('*');
            $productCollection->addLevelFilter('2');
            $this->setCollection($productCollection);
        }
        return $this->collection;
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        if(is_null($this->collection)) {
            $productCollection = $this->categoryCollectionFactory->create();
            $productCollection->addAttributeToSelect('*');
            $productCollection->addLevelFilter('2');
            $this->setCollection($productCollection);
        }
        return parent::_beforeToHtml();
    }

    /**
     * @param $productId
     * @return int|void
     */
    public function countColorProduct($productId)
    {
        $product = $this->productRepository->getById($productId); //Configurable Product Id

        $colorAttributeId = $product->getResource()->getAttribute('color')->getId(); // Get Color Attribute Id
        $configurableAttrs = $product->getTypeInstance()->getConfigurableAttributesAsArray($product); // Get Used Attributes with its values

        if(isset($configurableAttrs[$colorAttributeId])){
            return count($configurableAttrs[$colorAttributeId]['values']); // Gives you the count
        }
    }

    /**
     * @param $productId
     * @return bool
     */
    public function isCustomOption($productId)
    {
        $product = $this->productRepository->getById($productId);
        if($options = $product->getOptions()){
            foreach ($options as $option) {
                if($option->getIsColorpicker()){
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param $productId
     * @return bool
     */
    public function isCustomize($productId)
    {
        $product = $this->productRepository->getById($productId); //Configurable Product Id
        if($product->getSssCustomizeLink()){
            return true;
        }
        return $this->isCustomOption($productId);
    }

    /**
     * @return mixed
     */
    public function getUrlMedia()
    {
        return $this->helperData->getUrlMedia();
    }

    public function getMinPriceGiftCard($_item, $minPriceFill)
    {
        $amounts = [];
        $websiteId = $_item->getStore()->getWebsiteId();
        $amountsData = $this->getAttribute($_item, ProductAttributeInterface::CODE_AW_GC_AMOUNTS);
        $minAmounts = 0;
        foreach ($amountsData as $data) {
            if (in_array($data['website_id'], [$websiteId, 0])) {
                $amounts[] = $data['price'];
                $minAmounts = $data['price'];
            }
        }
        foreach ($amounts as $amount){
            if($amount < $minAmounts) {
                $minAmounts = $amount;
            }
        }
        if(!$minAmounts) {
            return $minPriceFill;
        }
        return $minAmounts;
    }

    /**
     * Retrieve product attribute by code
     *
     * @param Product $product
     * @param string $code
     * @return mixed
     */
    public function getAttribute($product, $code)
    {
        if (!$product->hasData($code)) {
            $product->getResource()->load($product, $product->getId());
        }
        return $product->getData($code);
    }

    public function resize($img_file, $widthCrop, $heightCrop, $crop)
    {
        return $this->resizeImage->getThumbnailImage($img_file, $widthCrop, $heightCrop = null, $crop = false);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function getImage($product, $imageId, $attributes = [])
    {
        return $this->imageBuilderCustom->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }
}