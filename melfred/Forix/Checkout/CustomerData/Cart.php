<?php
/**
 * Display Not Visible Individually Product
 */
namespace Forix\Checkout\CustomerData;

class Cart extends \Magento\Checkout\CustomerData\Cart {

	protected function getRecentItems()
	{
		$items = [];
		if (!$this->getSummaryCount()) {
			return $items;
		}

		foreach (array_reverse($this->getAllQuoteItems()) as $item) {
			/* @var $item \Magento\Quote\Model\Quote\Item */
			if (!$item->getProduct()->isVisibleInSiteVisibility()) {
				$product =  $item->getOptionByCode('product_type') !== null
					? $item->getOptionByCode('product_type')->getProduct()
					: $item->getProduct();

				$products = $this->catalogUrl->getRewriteByProductStore([$product->getId() => $item->getStoreId()]);
				if (!isset($products[$product->getId()])) {
					$items[] = $this->itemPoolInterface->getItemData($item); // "not visible individually"
					continue;
				}
				$urlDataObject = new \Magento\Framework\DataObject($products[$product->getId()]);
				$item->getProduct()->setUrlDataObject($urlDataObject);
			}
			$items[] = $this->itemPoolInterface->getItemData($item);
		}
		return $items;
	}
}