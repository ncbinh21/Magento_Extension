<?php

namespace Forix\Widget\Model\Block;

class Arrange implements \Magento\Framework\Option\ArrayInterface
{
	public function toOptionArray()
	{
		return [
			['value' => 'desc',  'label'  => __('Latest blog')],
			['value' => 'asc', 'label' => __('Old blog')]
		];
	}
}