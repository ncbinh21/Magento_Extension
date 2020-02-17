<?php

namespace Forix\RequisitionList\Model\ResourceModel\Quote;

class Item extends \Magento\Quote\Model\ResourceModel\Quote\Item
{
	public function getItemBySku($sku, $quoteId)
	{
		$adapter = $this->getConnection();
		$select  = $adapter
			->select()
			->from(['qi'=>'quote_item'],['op.value'])
			->joinLeft('quote_item_option as op','op.item_id = qi.item_id')
			->where('qi.sku = (?)', $sku)
			->where('qi.quote_id = (?)', $quoteId)
			->where('op.code = (?)', 'info_buyRequest');
		$query = $adapter->query($select);
		return $query->fetch();
	}

	public function getSkuParentItemId($id) {
		$adapter = $this->getConnection();
		$select  = $adapter
			->select()
			->from(['qi'=>'quote_item'],['qi.sku'])
			->where('qi.parent_item_id = (?)', $id);
		$query = $adapter->query($select);
		return $query->fetch();
	}


}