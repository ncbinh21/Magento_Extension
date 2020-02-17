<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Storelocator
 */


namespace Amasty\Storelocator\Block\Adminhtml\Location\Grid\Renderer;

use Magento\Framework\DataObject;
use Magento\Backend\Block\Context;
use Magento\Store\Model\System\Store;

class Stores extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{

	protected $_store;

	public function __construct(
		Context $context,
		Store $store
	)
	{
		parent::__construct($context);
		$this->_store = $store;
	}

	public function render(DataObject $row)
    {
        $stores = $row->getData('stores');
        if (!$stores) {
            return __('Restricts in All');
        }
        $html = '';
        $data = $this->_store->getStoresStructure(false, explode(',', $stores));
        foreach ($data as $website) {
            $html .= $website['label'] . '<br/>';
            foreach ($website['children'] as $group) {
                $html .= str_repeat('&nbsp;', 3) . $group['label'] . '<br/>';
                foreach ($group['children'] as $store) {
                    $html .= str_repeat('&nbsp;', 6) . $store['label'] . '<br/>';
                }
            }
        }

        if (!$html) {
            $html = __('All Store Views');
        }

        return $html;
    }

}