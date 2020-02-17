<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Forix\Catalog\Model;
use Magento\Catalog\Model\Product\Gallery\MimeTypeExtensionMap;
use Magento\Framework\Api\Data\ImageContentInterfaceFactory;
use Magento\Framework\Api\ImageContentValidatorInterface;
use Magento\Framework\Api\ImageProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class ProductRepository extends \Magento\Catalog\Model\ProductRepository
{
	/**
	 * @var CollectionProcessorInterface
	 */
	private $collectionProcessor;
	/**
	 * @var int
	 */
	private $cacheLimit = 0;
	/**
	 * @var \Magento\Framework\Serialize\Serializer\Json
	 */
	private $serializer;
	/**
	 * ProductRepository constructor.
	 * @param \Magento\Catalog\Model\ProductFactory $productFactory
	 * @param \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $initializationHelper
	 * @param \Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory $searchResultsFactory
	 * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
	 * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
	 * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository
	 * @param \Magento\Catalog\Model\ResourceModel\Product $resourceModel
	 * @param \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks $linkInitializer
	 * @param \Magento\Catalog\Model\Product\LinkTypeProvider $linkTypeProvider
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
	 * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $metadataServiceInterface
	 * @param \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
	 * @param \Magento\Catalog\Model\Product\Option\Converter $optionConverter
	 * @param \Magento\Framework\Filesystem $fileSystem
	 * @param ImageContentValidatorInterface $contentValidator
	 * @param ImageContentInterfaceFactory $contentFactory
	 * @param MimeTypeExtensionMap $mimeTypeExtensionMap
	 * @param ImageProcessorInterface $imageProcessor
	 * @param \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor
	 * @param CollectionProcessorInterface $collectionProcessor [optional]
	 * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
	 * @param int $cacheLimit [optional]
	 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function __construct(
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $initializationHelper,
		\Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory $searchResultsFactory,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
		\Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
		\Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
		\Magento\Catalog\Model\ResourceModel\Product $resourceModel,
		\Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks $linkInitializer,
		\Magento\Catalog\Model\Product\LinkTypeProvider $linkTypeProvider,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Api\FilterBuilder $filterBuilder,
		\Magento\Catalog\Api\ProductAttributeRepositoryInterface $metadataServiceInterface,
		\Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
		\Magento\Catalog\Model\Product\Option\Converter $optionConverter,
		\Magento\Framework\Filesystem $fileSystem,
		ImageContentValidatorInterface $contentValidator,
		ImageContentInterfaceFactory $contentFactory,
		MimeTypeExtensionMap $mimeTypeExtensionMap,
		ImageProcessorInterface $imageProcessor,
		\Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor,
		CollectionProcessorInterface $collectionProcessor = null,
		\Magento\Framework\Serialize\Serializer\Json $serializer = null,
		$cacheLimit = 1000
	) {
		$this->productFactory = $productFactory;
		$this->collectionFactory = $collectionFactory;
		$this->initializationHelper = $initializationHelper;
		$this->searchResultsFactory = $searchResultsFactory;
		$this->searchCriteriaBuilder = $searchCriteriaBuilder;
		$this->resourceModel = $resourceModel;
		$this->linkInitializer = $linkInitializer;
		$this->linkTypeProvider = $linkTypeProvider;
		$this->storeManager = $storeManager;
		$this->attributeRepository = $attributeRepository;
		$this->filterBuilder = $filterBuilder;
		$this->metadataService = $metadataServiceInterface;
		$this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
		$this->fileSystem = $fileSystem;
		$this->contentFactory = $contentFactory;
		$this->imageProcessor = $imageProcessor;
		$this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
		$this->collectionProcessor = $collectionProcessor ?: $this->getCollectionProcessor();
		$this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
			->get(\Magento\Framework\Serialize\Serializer\Json::class);
		$this->cacheLimit = (int)$cacheLimit;
		parent::__construct(
			$productFactory,
			$initializationHelper,
			$searchResultsFactory,
			$collectionFactory,
			$searchCriteriaBuilder,
			$attributeRepository,
			$resourceModel,
			$linkInitializer,
			$linkTypeProvider,
			$storeManager,
			$filterBuilder,
			$metadataServiceInterface,
			$extensibleDataObjectConverter,
			$optionConverter,
			$fileSystem,
			$contentValidator,
			$contentFactory,
			$mimeTypeExtensionMap,
			$imageProcessor,
			$extensionAttributesJoinProcessor,
			$collectionProcessor,
			$serializer
		);
	}
	/**
	 * {@inheritdoc}
	 */
	public function get($sku, $editMode = false, $storeId = null, $forceReload = false)
	{
		$cacheKey = $this->getCacheKey([$editMode, $storeId]);
		if (!isset($this->instances[$sku][$cacheKey]) || $forceReload) {
			$product = $this->productFactory->create();
			$productId = $this->resourceModel->getIdBySku($sku);
			if (!$productId) {
				throw new NoSuchEntityException(__('Requested product doesn\'t exist'));
			}
			if ($editMode) {
				$product->setData('_edit_mode', true);
			}
			if ($storeId !== null) {
				$product->setData('store_id', $storeId);
			}
			$product->load($productId);
			$this->cacheProduct($cacheKey, $product);
			$sku = $product->getSku();
		}
		if (!isset($this->instances[$sku])) {
			$sku = trim($sku);
		}
		return $this->instances[$sku][$cacheKey];
	}
	/**
	 * @param \Magento\Catalog\Model\Product $product
	 * @param bool $createNew
	 * @return void
	 */
	private function assignProductToWebsites(\Magento\Catalog\Model\Product $product, $createNew)
	{
		$websiteIds = $product->getWebsiteIds();
		if (!$this->storeManager->hasSingleStore()) {
			$websiteIds = array_unique(
				array_merge(
					$websiteIds,
					[$this->storeManager->getStore()->getWebsiteId()]
				)
			);
		}
		if ($createNew && $this->storeManager->getStore(true)->getCode() == \Magento\Store\Model\Store::ADMIN_CODE) {
			$websiteIds = array_keys($this->storeManager->getWebsites());
		}
		$product->setWebsiteIds($websiteIds);
	}
	/**
	 * Add product to internal cache and truncate cache if it has more than cacheLimit elements.
	 *
	 * @param string $cacheKey
	 * @param \Magento\Catalog\Api\Data\ProductInterface $product
	 * @return void
	 */
	private function cacheProduct($cacheKey, \Magento\Catalog\Api\Data\ProductInterface $product)
	{
		$this->instancesById[$product->getId()][$cacheKey] = $product;
		$this->instances[$product->getSku()][$cacheKey] = $product;
		if ($this->cacheLimit && count($this->instances) > $this->cacheLimit) {
			$offset = round($this->cacheLimit / -2);
			$this->instancesById = array_slice($this->instancesById, $offset, null, true);
			$this->instances = array_slice($this->instances, $offset, null, true);
		}
	}
	/**
	 * Process product links, creating new links, updating and deleting existing links
	 *
	 * @param \Magento\Catalog\Api\Data\ProductInterface $product
	 * @param \Magento\Catalog\Api\Data\ProductLinkInterface[] $newLinks
	 * @return $this
	 * @throws NoSuchEntityException
	 */
	private function processLinks(\Magento\Catalog\Api\Data\ProductInterface $product, $newLinks)
	{
		if ($newLinks === null) {
			// If product links were not specified, don't do anything
			return $this;
		}
		// Clear all existing product links and then set the ones we want
		$linkTypes = $this->linkTypeProvider->getLinkTypes();
		foreach (array_keys($linkTypes) as $typeName) {
			$this->linkInitializer->initializeLinks($product, [$typeName => []]);
		}
		// Set each linktype info
		if (!empty($newLinks)) {
			$productLinks = [];
			foreach ($newLinks as $link) {
				$productLinks[$link->getLinkType()][] = $link;
			}
			foreach ($productLinks as $type => $linksByType) {
				$assignedSkuList = [];
				/** @var \Magento\Catalog\Api\Data\ProductLinkInterface $link */
				foreach ($linksByType as $link) {
					$assignedSkuList[] = $link->getLinkedProductSku();
				}
				$linkedProductIds = $this->resourceModel->getProductsIdsBySkus($assignedSkuList);
				$linksToInitialize = [];
				foreach ($linksByType as $link) {
					$linkDataArray = $this->extensibleDataObjectConverter
						->toNestedArray($link, [], \Magento\Catalog\Api\Data\ProductLinkInterface::class);
					$linkedSku = $link->getLinkedProductSku();
					if (!isset($linkedProductIds[$linkedSku])) {
						throw new NoSuchEntityException(
							__('Product with SKU "%1" does not exist', $linkedSku)
						);
					}
					$linkDataArray['product_id'] = $linkedProductIds[$linkedSku];
					$linksToInitialize[$linkedProductIds[$linkedSku]] = $linkDataArray;
				}
				$this->linkInitializer->initializeLinks($product, [$type => $linksToInitialize]);
			}
		}
		$product->setProductLinks($newLinks);
		return $this;
	}
	/**
	 * @return \Magento\Catalog\Model\ProductRepository\MediaGalleryProcessor
	 */
	private function getMediaGalleryProcessor()
	{
		if (null === $this->mediaGalleryProcessor) {
			$this->mediaGalleryProcessor = \Magento\Framework\App\ObjectManager::getInstance()
				->get(\Magento\Catalog\Model\ProductRepository\MediaGalleryProcessor::class);
		}
		return $this->mediaGalleryProcessor;
	}
	/**
	 * Retrieve collection processor
	 *
	 * @deprecated 101.1.0
	 * @return CollectionProcessorInterface
	 */
	private function getCollectionProcessor()
	{
		if (!$this->collectionProcessor) {
			$this->collectionProcessor = \Magento\Framework\App\ObjectManager::getInstance()->get(
				'Magento\Catalog\Model\Api\SearchCriteria\ProductCollectionProcessor'
			);
		}
		return $this->collectionProcessor;
	}
	/**
	 * Get key for cache
	 *
	 * @param array $data
	 * @return string
	 */
	protected function getCacheKey($data)
	{
		$serializeData = [];
		foreach ($data as $key => $value) {
			if (is_object($value)) {
				$serializeData[$key] = $value->getId();
			} else {
				$serializeData[$key] = $value;
			}
		}
		$serializeData = $this->serializer->serialize($serializeData);
		return sha1($serializeData);
	}
}