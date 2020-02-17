<?php

namespace Forix\RequisitionList\Rewrite\Model;

use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\CartItemInterface;

class AddToCartProcessor extends \Magento\RequisitionList\Model\AddToCartProcessor
{
	private   $cartItemOptionProcessor;
	protected $registry;
	protected $cart;

	public function __construct(
		\Magento\Quote\Model\Quote\Item\CartItemOptionsProcessor $cartItemOptionProcessor,
		\Magento\Framework\Registry $registry,
		\Magento\Checkout\Model\Cart $cart
	)
	{
		parent::__construct($cartItemOptionProcessor);
		$this->cartItemOptionProcessor = $cartItemOptionProcessor;
		$this->registry = $registry;
		$this->cart = $cart;
	}

	/**
	 * Add a product from a requisition list to cart.
	 *
	 * @param CartInterface $cart
	 * @param CartItemInterface $cartItem
	 * @return void
	 */
	public function execute(CartInterface $cart, CartItemInterface $cartItem)
	{
		$product = $cartItem->getData('product');
		if ($product->getTypeId() == "grouped") {
			$item = $this->registry->registry('data_requisition_listItem');
			$options = $item->getOptions();
			$productOptions = $options["info_buyRequest"];
			$this->cart->addProduct($product, $productOptions);
			$this->registry->unregister('data_requisition_listItem');

		} else {
			$productOptions = $this->cartItemOptionProcessor->getBuyRequest($product->getTypeId(), $cartItem);
			$cart->addProduct($product, $productOptions);
		}

	}

}