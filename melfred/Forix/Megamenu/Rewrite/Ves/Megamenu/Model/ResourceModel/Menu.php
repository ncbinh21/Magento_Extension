<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: html
 */
namespace Forix\Megamenu\Rewrite\Ves\Megamenu\Model\ResourceModel;

class Menu extends \Ves\Megamenu\Model\ResourceModel\Menu {
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Filesystem $filesystem,
		\Forix\Megamenu\Rewrite\Ves\Megamenu\Helper\Fields $editor,
		\Ves\Megamenu\Helper\Data $vesData,
		$connectionName = null
	) {
		\Magento\Framework\Model\ResourceModel\Db\AbstractDb::__construct($context, $connectionName);
		$this->_storeManager    = $storeManager;
		$this->_filesystem      = $filesystem;
		$this->editor           = $editor;
		$this->_vesData         = $vesData;
	}
	public function extractItem($items){
		if(is_array($items)){
			foreach ($items as $item) {
				if( !in_array($item['link_type'],array('attribute_link','shopby_link','brand_link'))){
					$item['attribute_code'] = null;
					$item['attribute_value'] = null;
				}
				unset($item['attributes']);
				unset($item['options']);
				unset($item['selectedAttributes']);
				if(isset($item['children']) && is_array($item['children'])){
					$this->extractItem($item['children']);
				}
				unset($item['children']);
				$this->_data[] = $item;
			}
		}
	}
}