<?php
/**
 * Project: melfredborzall.
 * User: Hidro Le
 * Title: Magento Develop
 * Date: 9/13/17
 * Time: 12:12 AM
 */

namespace Forix\Product\Ui\DataProvider\Product\Form\Modifier\Data;


use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use Magento\ConfigurableProduct\Model\Product\Type\VariationMatrix;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Framework\UrlInterface;

class AssociatedProducts extends \Magento\ConfigurableProduct\Ui\DataProvider\Product\Form\Modifier\Data\AssociatedProducts
{

	protected $_matrixHelper;
	public function __construct(
		LocatorInterface $locator,
		UrlInterface $urlBuilder,
		ConfigurableType $configurableType,
		ProductRepositoryInterface $productRepository,
		StockRegistryInterface $stockRegistry,
		VariationMatrix $variationMatrix,
		CurrencyInterface $localeCurrency,
		JsonHelper $jsonHelper,
		ImageHelper $imageHelper,
		\Forix\Product\Helper\Product\Matrix $matrixHelper
	)
	{
		$this->_matrixHelper = $matrixHelper;
		parent::__construct($locator, $urlBuilder, $configurableType, $productRepository, $stockRegistry, $variationMatrix, $localeCurrency, $jsonHelper, $imageHelper);
	}

	/**
	 * Get variations product matrix
	 *
	 * @return array
	 */
	public function getProductMatrix()
	{
		$productMatrix = parent::getProductMatrix();
		foreach ($productMatrix as &$matrix) {
			$recommendSKU = $this->_matrixHelper->getRecommendSku($this->locator->getProduct()->getId(), $matrix['id']);
			$recommendSKU = null !== $recommendSKU ? $recommendSKU : \Forix\Product\Model\Product\LinkOptions\Recommend::STATUS_NOT_RECOMMEND;
			$matrix['recommend_sku'] = $recommendSKU;
		}

		return $productMatrix;
	}

	protected function prepareVariations()
	{
		$variations = $this->getVariations();
		$productMatrix = [];
		$attributes = [];
		$productIds = [];
		if ($variations) {
			$objetManager =  \Magento\Framework\App\ObjectManager::getInstance();
			$escaper = $objetManager->get("Magento\Framework\Escaper");

			$usedProductAttributes = $this->getUsedAttributes();
			$productByUsedAttributes = $this->getAssociatedProducts();
			$currency = $this->localeCurrency->getCurrency($this->locator->getBaseCurrencyCode());
			$configurableAttributes = $this->getAttributes();

			foreach ($variations as $variation) {
				$attributeValues = [];
				foreach ($usedProductAttributes as $attribute) {
					$attributeValues[$attribute->getAttributeCode()] = $variation[$attribute->getId()]['value'];
				}
				$key = implode('-', $attributeValues);

				foreach ($productByUsedAttributes as $itemProductByUsed) {
					if (isset($itemProductByUsed[$key])) {
						$product = $itemProductByUsed[$key];
						$price = $product->getPrice();
						$variationOptions = [];

						foreach ($usedProductAttributes as $attribute) {
							if (!isset($attributes[$attribute->getAttributeId()])) {
								$attributes[$attribute->getAttributeId()] = [
									'code' => $attribute->getAttributeCode(),
									'label' => $attribute->getStoreLabel(),
									'id' => $attribute->getAttributeId(),
									'position' => $configurableAttributes[$attribute->getAttributeId()]['position'],
									'chosen' => [],
								];
								foreach ($attribute->getOptions() as $option) {
									if (!empty($option->getValue())) {
										$attributes[$attribute->getAttributeId()]['options'][$option->getValue()] = [
											'attribute_code' => $attribute->getAttributeCode(),
											'attribute_label' => $attribute->getStoreLabel(0),
											'id' => $option->getValue(),
											'label' => $option->getLabel(),
											'value' => $option->getValue(),
										];
									}
								}
							}
							$optionId = $variation[$attribute->getId()]['value'];
							$variationOption = [
								'attribute_code' => $attribute->getAttributeCode(),
								'attribute_label' => $attribute->getStoreLabel(0),
								'id' => $optionId,
								'label' => $variation[$attribute->getId()]['label'],
								'value' => $optionId,
							];
							$variationOptions[] = $variationOption;
							$attributes[$attribute->getAttributeId()]['chosen'][$optionId] = $variationOption;
						}

						$productMatrix[] = [
							'id' => $product->getId(),
							'product_link' => '<a href="' . $this->urlBuilder->getUrl(
									'catalog/product/edit',
									['id' => $product->getId()]
								) . '" target="_blank">' . $escaper->escapeHtml($product->getName()) . '</a>',
							'sku' => $escaper->escapeHtml($product->getSku()),
							'mb_rig_model'=>($product->getAttributeText("mb_rig_model")) ? $product->getAttributeText("mb_rig_model") : '',
							'name' => $escaper->escapeHtml($product->getName()),
							'qty' => $this->getProductStockQty($product),
							'price' => $price,
							'price_string' => $currency->toCurrency(sprintf("%f", $price)),
							'price_currency' => $this->locator->getStore()->getBaseCurrency()->getCurrencySymbol(),
							'configurable_attribute' => $this->getJsonConfigurableAttributes($variationOptions),
							'weight' => $product->getWeight(),
							'status' => $product->getStatus(),
							'variationKey' => $this->getVariationKey($variationOptions),
							'canEdit' => 0,
							'newProduct' => 0,
							'attributes' => $this->getTextAttributes($variationOptions),
							'thumbnail_image' => $this->imageHelper->init($product, 'product_thumbnail_image')->getUrl(),
						];
						$productIds[] = $product->getId();
					}
				}
			}
		}

		$this->productMatrix = $productMatrix;
		$this->productIds = $productIds;
		$this->productAttributes = array_values($attributes);
	}


	protected function getAssociatedProducts()
	{
		$productByUsedAttributes = [];
		foreach ($this->_getAssociatedProducts() as $product) {
			$keys = [];
			foreach ($this->getUsedAttributes() as $attribute) {
				/** @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
				$keys[] = $product->getData($attribute->getAttributeCode());

			}
			$productByUsedAttributes[][implode('-', $keys)] = $product;
		}

		return $productByUsedAttributes;
	}

}