<?php

namespace Forix\Minicart\Plugin;

use Magento\Quote\Model\Quote\Item;


class DefaultItem
{
	public function aroundGetItemData($subject, \Closure $proceed, Item $item)
	{

		$data      = $proceed($item);
		$product   = $item->getProduct();
		$attributes = $product->getAttributes();
		$attrMb = [];
		foreach ($attributes as $a) {
			if ($pos = strpos($a->getName(), 'mb_') !== false) {
				$attrName =  $a->getName();
				$label    =  $a->getStoreLabel();
				$attr = $product->getAttributeText($attrName);
				if (!empty($attr) || $attr!="") {
					$attrMb[$label] = $product->getAttributeText($attrName);
				}
			}
		}

		$outPut = "";
		foreach ($attrMb as $label=>$att) {
			if (is_array($att)) {
				$att = implode(", ",$att);
			}
			$outPut.= '<span>'.$label.': '.$att.'</span>';
		}

		$atts = [
			'mb_attributes' => $outPut
		];
		return array_merge($data, $atts);

	}
}