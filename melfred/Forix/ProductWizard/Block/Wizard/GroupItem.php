<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 13/06/2018
 * Time: 10:11
 */

namespace Forix\ProductWizard\Block\Wizard;

use Forix\ProductWizard\Block\Context;
use \Forix\ProductWizard\Model\Source\Templates as GroupItemTemplates;
use Magento\Framework\Phrase;

/**
 * Class Item
 * @method \Forix\ProductWizard\Model\GroupItem getSource()
 * @package Forix\ProductWizard\Block\Wizard
 */
class GroupItem extends \Forix\ProductWizard\Block\AbstractView
{
    /**
     * @var \Magento\Catalog\Block\Product\ImageBuilder
     */
    protected $_imageBuilder;


    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;


    /**
     * GroupItem constructor.
     * @param Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Forix\ProductWizard\Model\ProductCollectionProvider $productCollectionProvider
     * @param \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder
     * @param \Forix\ProductWizard\Model\GroupFactory $groupFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\Config $config
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder,
        \Forix\ProductWizard\Model\GroupFactory $groupFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Eav\Model\Config $eavConfig,
        array $data = [])
    {
        $this->_imageBuilder = $imageBuilder;
        $this->_eavConfig = $eavConfig;
        parent::__construct($context, $groupFactory, $registry, $data);
    }

    /**
     * @param $attributeCode
     * @return string
     */
    public function getConditionType($attributeCode)
    {
        $entityType = \Magento\Catalog\Model\Product::ENTITY;
        try {
            $attribute = $this->_eavConfig->getAttribute($entityType, $attributeCode);
            if ($attribute->getFrontendInput() == 'multiselect') {
                return 'finset';
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
        }
        return 'eq';
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getProductPrice(\Magento\Catalog\Model\Product $product)
    {
        $priceRender = $this->getPriceRender();

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                [
                    'include_container' => false,
                    'display_minimal_price' => false,
                    'zone' => \Magento\Framework\Pricing\Render::ZONE_ITEM_VIEW,
                    'list_category_page' => true,
                    'price_type_code' => 'final_price',
                ]
            );
        }

        return $price;
    }

    /**
     * Specifies that price rendering should be done for the list of products
     * i.e. rendering happens in the scope of product list, but not single product
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getPriceRender()
    {
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        if (!$priceRender) {
            $priceRender = $this->getLayout()->createBlock('Magento\Framework\Pricing\Render', 'product.price.render.default', [
                'is_product_list' => true,
                'price_render_handle' => 'catalog_product_prices',
                'use_link_for_as_low_as' => true
            ]);
        }
        return $priceRender;
    }

    /**
     * @return array|\Forix\ProductWizard\Model\ResourceModel\GroupItemOption\Collection|\Magento\Catalog\Model\ResourceModel\Product\Collection|null
     */
    public function getGroupItemOptions()
    {
        switch ($this->getSource()->getTemplate()) {
            case GroupItemTemplates::DROP_DOWN:
                return $this->getSource()->getOptionCollection();
                break;
            case GroupItemTemplates::PRODUCT_DETAIL_SELECT:
            case GroupItemTemplates::PRODUCT_DETAIL_CHECKBOX:
                $optionCollection = $this->getSource()->getOptionCollection();
                $optionBySku = [];
                foreach ($optionCollection as $option) {
                    $optionBySku[$option->getProductSku()] = $option;
                }
                $products= [];
                $skus = array_keys($optionBySku);
                foreach ($this->getCurrentWizard()->getAvailableProductCollection() as $productRender){
                    if(in_array($productRender->getProduct()->getSku(), $skus)){
                        $productRender->setData('wizard_option', $optionBySku[$productRender->getProduct()->getSku()]);
                        $products[] = $productRender;
                    }
                }
                return $products;
        }
        return null;
    }

    /**
     * Retrieve product image
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId category_page_list
     * @param array $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function getImage($product, $imageId = 'category_page_list', $attributes = [])
    {
        return $this->_imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }


    /**
     * $includeAttr is optional array of attribute codes to
     * include them from additional data array
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $includeAttr
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAdditionalData($product, array $includeAttr = [])
    {
        $data = [];
        $attributes = $product->getAttributes();
        foreach ($attributes as $attribute) {
            if ($attribute->getIsVisibleOnFront() && in_array($attribute->getAttributeCode(), $includeAttr)) {
                $value = $attribute->getFrontend()->getValue($product);

                if (!$product->hasData($attribute->getAttributeCode())) {
                    $value = __('N/A');
                } elseif ((string)$value == '') {
                    $value = __('No');
                }
                if ($value instanceof Phrase || (is_string($value) && strlen($value))) {
                    $data[$attribute->getAttributeCode()] = [
                        'label' => __($attribute->getStoreLabel()),
                        'value' => $value,
                        'code' => $attribute->getAttributeCode(),
                    ];
                }
            }
        }
        return $data;
    }
}