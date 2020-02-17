<?php

namespace Forix\Quote\Model;

class itemOption extends \Magento\Quote\Model\ResourceModel\Quote\Item {

	public function getInfoRequestByQuoteId($id) {
		$adapter = $this->getConnection();
		$select  = $adapter
			->select()
			->from(['qo'=>'quote_item_option'],['qo.value'])
			->where('qo.code = (?)', 'info_buyRequest')
			->where('qo.item_id = (?)', $id);
		$query = $adapter->query($select);
		return $query->fetch();
	}


}