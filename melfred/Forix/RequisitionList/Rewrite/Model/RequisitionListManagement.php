<?php

namespace Forix\RequisitionList\Rewrite\Model;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\RequisitionList\Api\Data\RequisitionListInterface;
use Magento\RequisitionList\Api\Data\RequisitionListItemInterface;
use Magento\RequisitionList\Api\Data\RequisitionListItemInterfaceFactory;
use Magento\RequisitionList\Api\RequisitionListRepositoryInterface;
use Magento\RequisitionList\Model\RequisitionListItem\CartItemConverter;
use Magento\RequisitionList\Model\RequisitionListItem\Validation;
use Magento\RequisitionList\Model\RequisitionListItem\Merger as ItemMerger;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Forix\Shopby\Model\ResourceModel\ResourceHelperFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Registry;

class RequisitionListManagement extends \Magento\RequisitionList\Model\RequisitionListManagement {

	/**
	 * @var RequisitionListRepositoryInterface
	 */
	private $requisitionListRepository;

	/**
	 * @var RequisitionListItemInterfaceFactory
	 */
	private $requisitionListItemFactory;

	/**
	 * @var \Magento\Quote\Api\CartRepositoryInterface
	 */
	private $cartRepository;

	/**
	 * @var \Magento\RequisitionList\Model\RequisitionListItem\CartItemConverter
	 */
	private $cartItemConverter;

	/**
	 * @var \Magento\RequisitionList\Model\RequisitionListItem\Validation
	 */
	private $validation;

	/**
	 * @var \Magento\RequisitionList\Model\RequisitionListItem\Merger
	 */
	private $itemMerger;

	/**
	 * @var DateTime
	 */
	private $dateTime;

	/**
	 * @var array
	 */
	private $addToCartProcessors = [];

	/**
	 * @var string
	 */
	private $defaultAddToCartProcessorKey = 'simple';

	/**
	 * @var \Forix\Shopby\Model\ResourceModel\Collection
	 */
	protected $shopByCollection;
	protected $shopByCollectionFactory;

	/**
	 * @var Json
	 */
	protected $serializer;

	protected $productRepository;



	protected $registry;

	/**
     * RequisitionListManagement constructor.
     * @param RequisitionListRepositoryInterface $requisitionListRepository
     * @param RequisitionListItemInterfaceFactory $requisitionListItemFactory
     * @param CartRepositoryInterface $cartRepository
     * @param CartItemConverter $cartItemConverter
     * @param Validation $validation
     * @param ItemMerger $itemMerger
     * @param DateTime $dateTime
     * @param array $addToCartProcessors
     * @param ResourceHelperFactory $collectionFactory
     * @param Json|null $serializer
     */
	public function __construct(
		RequisitionListRepositoryInterface $requisitionListRepository,
		RequisitionListItemInterfaceFactory $requisitionListItemFactory,
		CartRepositoryInterface $cartRepository,
		CartItemConverter $cartItemConverter,
		Validation $validation,
		ItemMerger $itemMerger,
		DateTime $dateTime,
		array $addToCartProcessors,
        ResourceHelperFactory $collectionFactory,
		Json $serializer = null,
		ProductRepositoryInterface $productRepository,
		Registry $registry

	) {
		parent::__construct($requisitionListRepository, $requisitionListItemFactory, $cartRepository,
			$cartItemConverter, $validation, $itemMerger, $dateTime, $addToCartProcessors);
		$this->requisitionListRepository = $requisitionListRepository;
		$this->requisitionListItemFactory = $requisitionListItemFactory;
		$this->cartRepository = $cartRepository;
		$this->cartItemConverter = $cartItemConverter;
		$this->validation = $validation;
		$this->itemMerger = $itemMerger;
		$this->dateTime = $dateTime;
		$this->addToCartProcessors = $addToCartProcessors;
		$this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
		$this->shopByCollectionFactory    = $collectionFactory;
		$this->productRepository = $productRepository;
		$this->registry  = $registry;

	}

	/**
	 * @inheritdoc
	 */
	public function addItemToList(
		RequisitionListInterface $requisitionList,
		RequisitionListItemInterface $requisitionListItem
	) {
		$items = $requisitionList->getItems();
		$requisitionListItems = $this->itemMerger->mergeItem($items, $requisitionListItem);
		$requisitionList->setItems($requisitionListItems);
		$this->requisitionListRepository->save($requisitionList);
		return $requisitionList;
	}

	/**
	 * @inheritdoc
	 */
	public function setItemsToList(
		RequisitionListInterface $requisitionList,
		array $requisitionListItems
	) {
		$requisitionListItems = $this->itemMerger->merge($requisitionListItems);
		$requisitionList->setItems($requisitionListItems);
		return $requisitionList;
	}

	/**
	 * @inheritdoc
	 */
	public function copyItemToList(
		RequisitionListInterface $requisitionList,
		RequisitionListItemInterface $requisitionListItem
	) {
		/** @var RequisitionListItemInterface $requisitionListItem */
		$targetListItem = $this->requisitionListItemFactory->create();
		$targetListItem->setQty($requisitionListItem->getQty());
		$targetListItem->setOptions((array)$requisitionListItem->getOptions());
		$targetListItem->setSku($requisitionListItem->getSku());

		$this->addItemToList($requisitionList, $targetListItem);

		return $requisitionList;
	}

	/**
	 * @inheritdoc
	 */
	public function placeItemsInCart($cartId, array $items, $isReplace = false)
	{

		$cart = $this->cartRepository->get($cartId);
		if ($isReplace) {
			/** @var $cart \Magento\Quote\Model\Quote */
			$cart->removeAllItems();
		}

		$addedItems = [];
		foreach ($items as $item) {
			if ($this->validation->isValid($item)) {
				$this->addItemToCart($cart, $item);
				$addedItems[] = $item;
			}
		}


		$this->updateListActivity($items);
		$this->cartRepository->save($cart);
		return $addedItems;
	}

	private function getRigModelOption($id) {
		if ($id!="") {
			return $this->shopByCollectionFactory->create()->getOptionById($id);
		}
		return null;
	}

	/**
	 * Add requisition list item to cart.
	 *
	 * @param CartInterface $cart
	 * @param RequisitionListItemInterface $item
	 * @return $this
	 */
	private function addItemToCart(CartInterface $cart, RequisitionListItemInterface $item)
	{
		$cartItem = $this->cartItemConverter->convert($item);
		$options = $item->getOptions();
		if (isset($options["product_type"]) && $options["product_type"] == "grouped") {
			if (!$this->registry->registry('data_requisition_listItem')) {
				$this->registry->register("data_requisition_listItem", $item);
			}
		}

		if (isset($options["info_buyRequest"]["rig"])) {
			$rig = $options["info_buyRequest"]["rig"];
			$rigAttr    = array_keys($rig)[0];
			$optionId   = $rig[$rigAttr];
			$eavOptionValue = $this->getRigModelOption($optionId);
			$additionalOptions[] = array(
				'label'       => __("Your Rig Model"),
				'value'       => $eavOptionValue["value"],
				'option_id'   => $eavOptionValue["option_id"],
				'option_value'=> $rigAttr
			);
			$data = $this->serializer->serialize($additionalOptions);
			$cartItem->getData('product')->addCustomOption('additional_options', $data);
		}


		$product  = $cartItem->getData('product');
		$productType = $product->getTypeId();


		/**
		 * @var \Magento\RequisitionList\Model\AddToCartProcessorInterface $addToCartProcessor
		 */
		$addToCartProcessor = (isset($this->addToCartProcessors[$productType]))
			? $this->addToCartProcessors[$productType]
			: $this->addToCartProcessors[$this->defaultAddToCartProcessorKey];
		$addToCartProcessor->execute($cart, $cartItem);

		return $this;
	}

	/**
	 * Update requisition lists last activity.
	 *
	 * @param RequisitionListItemInterface[] $items
	 * @return $this
	 */
	private function updateListActivity(array $items)
	{
		$listIds = array_map(function (RequisitionListItemInterface $item) {
			return $item->getRequisitionListId();
		}, $items);
		$listIds = array_unique($listIds);

		foreach ($listIds as $listId) {
			$list = $this->requisitionListRepository->get($listId);
			$list->setUpdatedAt($this->dateTime->timestamp());
			$this->requisitionListRepository->save($list);
		}

		return $this;
	}



	protected function _initProduct($productId)
	{
		if ($productId) {
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$storeId = $objectManager->get(
				\Magento\Store\Model\StoreManagerInterface::class
			)->getStore()->getId();
			try {
				return $this->productRepository->getById($productId, false, $storeId);
			} catch (NoSuchEntityException $e) {
				return false;
			}
		}
		return false;
	}


}